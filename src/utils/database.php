<?php
require_once("utils.php");

/**
 * Constant variables
 */
define("DUPLICATE_ERROR", 1062);

/**
 * Connect to database
 * Return connection instance
 */
function connect_db()
{
  try {
    load_env();
    $host = $_ENV["DB_HOST"];
    $user = $_ENV["DB_USER"];
    $pass = $_ENV["DB_PASS"];
    $dbname = $_ENV["DB_NAME"];
    return new mysqli($host, $user, $pass, $dbname);
  } catch (Exception $e) {
    throw new Exception("Unable to connect to database", 500);
  }
}

/**
 * Sanitize MySQL input
 */
function sanitize_sql($conn, $string)
{
  return $conn->real_escape_string($string);
}

/**
 * Authenticate user with password
 */
function login($email, $password)
{
  try {
    $conn = connect_db();
    $email = sanitize_sql($conn, $email);
    $password = sanitize_sql($conn, $password);
    $query = "SELECT password FROM user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();
    $stmt->close();
    $conn->close();
    $hashedPassword = hash("sha512", $password);
    if (count($users) == 0 || $users[0]["password"] != $hashedPassword) {
      return "Incorrect user name or password.";
    }
    return "";
  } catch (Exception $e) {
    throw $e;
  }
}

/**
 * Check whether authenticated user is an admin
 */
function isAdmin($email)
{
  try {
    $conn = connect_db();
    $query = <<<SQL
    SELECT id FROM admin
    JOIN user USING(id) WHERE email = ?
    SQL;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows;
    $result->close();
    $stmt->close();
    $conn->close();
    return $count == 1;
  } catch (Exception $e) {
    throw $e;
  }
}

/**
 * List all customers
 * If search is specified,
 * Return customers based on search term
 */
function list_customers($search = "")
{
  try {
    $conn = connect_db();
    $query = <<<SQL
    SELECT
      first_name, last_name, email,
      address, home_phone, cell_phone
    FROM customer JOIN user USING(id)
    SQL;
    $stmt = $conn->prepare($query);
    $search = sanitize_sql($conn, $search);
    if ($search != "") {
      $conditions = "";
      $conditionValues = [];
      $searchFields = [
        "first_name", "last_name",
        "email", "home_phone", "cell_phone",
      ];
      foreach ($searchFields as $i => $field) {
        $isLast = $i == count($searchFields) - 1;
        $conditions .= "$field LIKE ?" . ($isLast ? "" : " OR ");
        $conditionValues[] = "%$search%";
      }
      $stmt = $conn->prepare("$query WHERE $conditions");
      $stmt->bind_param("sssss", ...$conditionValues);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $customers = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();
    $stmt->close();
    $conn->close();
    return $customers;
  } catch (Exception $e) {
    throw $e;
  }
}

/**
 * Add a customer to the database
 * {
 *    email, password,
 *    first_name, last_name,
 *    home_phone, cell_phone, address
 * }
 */
function add_customer($inputs)
{
  try {
    $conn = connect_db();
    try {
      $email = sanitize_sql($conn, $inputs["email"]);
      $password = sanitize_sql($conn, $inputs["password"]);
      $hashedPassword = hash("sha512", $password);
      $conn->begin_transaction();
      $query = "INSERT INTO user (email, password) VALUES (LOWER(?), ?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $email, $hashedPassword);
      $stmt->execute();
      $values = [
        $inputs["first_name"],
        $inputs["last_name"],
        $inputs["home_phone"],
        $inputs["cell_phone"],
        $inputs["address"],
      ];
      foreach ($values as $i => $value) {
        $values[$i] = sanitize_sql($conn, $value);
      }
      $query = <<<SQL
      INSERT INTO customer
      (id, first_name, last_name, home_phone, cell_phone, address)
       VALUES (LAST_INSERT_ID(), ?, ?, ?, ?, ?)
      SQL;
      $stmt = $conn->prepare($query);
      $stmt->bind_param("sssss", ...$values);
      $stmt->execute();
      $stmt->close();
      $conn->commit();
      $conn->close();
      return "";
    } catch (Exception $e) {
      $conn->rollback();
      if ($e->getCode() == DUPLICATE_ERROR) {
        return "Email already existed";
      }
      throw $e;
    }
  } catch (Exception $e) {
    throw $e;
  }
}

/**
 * List all products
 */
function list_products()
{
  try {
    $conn = connect_db();
    $query = <<<SQL
    SELECT id, name, image, price
    FROM product LEFT OUTER JOIN coffee USING(id)
    ORDER BY category, type, roast_level
    SQL;
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();
    $stmt->close();
    $conn->close();
    return $products;
  } catch (Exception $e) {
    throw $e;
  }
}

/**
 * List products based on category
 * Category options: coffee, brewing-tool
 */
function list_products_by_category($category = "coffee")
{
  try {
    $conn = connect_db();
    $query = <<<SQL
    SELECT id, name, image, price
    FROM product JOIN coffee USING(id)
    ORDER BY type, roast_level
    SQL;
    if ($category == "brewing-tool") {
      $query = <<<SQL
      SELECT id, name, image, price
      FROM product WHERE category = "tool"
      SQL;
    }
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();
    $stmt->close();
    $conn->close();
    return $products;
  } catch (Exception $e) {
    throw $e;
  }
}

/**
 * List most visited products
 */
function list_products_by_most_visited($limit = 5)
{
  try {
    $conn = connect_db();
    $query = <<<SQL
    SELECT id, name, image, price
    FROM product ORDER BY visited DESC LIMIT ?
    SQL;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();
    $stmt->close();
    $conn->close();
    return $products;
  } catch (Exception $e) {
    throw $e;
  }
}

/**
 * List products from a list of ids
 */
function list_products_by_id($idList)
{
  if (count($idList) == 0) {
    return [];
  }
  try {
    $conn = connect_db();
    $wildcards = $types = "";
    foreach ($idList as $i => $id) {
      $idList[$i] = sanitize_sql($conn, $id);
      $isLast = $i == count($idList) - 1;
      $wildcards .= "?" . ($isLast ? "" : ",");
      $types .= "s";
    }
    $query = <<<SQL
    SELECT id, name, image, price
    FROM product WHERE id IN($wildcards)
    SQL;
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$idList);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();
    $stmt->close();
    $conn->close();
    $mappedProducts = [];
    foreach ($products as $product) {
      $mappedProducts[$product["id"]] = $product;
    }
    $products = [];
    foreach ($idList as $id) {
      $products[] = $mappedProducts[$id];
    }
    return $products;
  } catch (Exception $e) {
    throw $e;
  }
}

/**
 * Get a product based on product id
 */
function get_product_by_id($id)
{
  try {
    $conn = connect_db();
    $query = <<<SQL
    SELECT id, name, image, price, description
    FROM product WHERE id = ?
    SQL;
    $stmt = $conn->prepare($query);
    $id = sanitize_sql($conn, $id);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
      throw new Exception("Product not found", 404);
    }
    [$product] = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();
    $stmt->close();
    $conn->close();
    return $product;
  } catch (Exception $e) {
    throw $e;
  }
}

/**
 * Increment product visited count by one
 */
function update_product_visited_count($id)
{
  try {
    $conn = connect_db();
    $query = <<<SQL
    UPDATE product
    SET visited = visited + 1
    WHERE id = ?
    SQL;
    $stmt = $conn->prepare($query);
    $id = sanitize_sql($conn, $id);
    $stmt->bind_param("s", $id);
    $stmt->execute();
  } catch (Exception $e) {
    throw $e;
  } finally {
    $stmt->close();
    $conn->close();
  }
}
