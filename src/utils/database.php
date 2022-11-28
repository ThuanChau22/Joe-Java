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
    http_response_code(500);
    include_once("error.php");
    die();
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
function login($username, $password)
{
  try {
    $conn = connect_db();
    $username = sanitize_sql($conn, $username);
    $password = sanitize_sql($conn, $password);
    $query = "SELECT password FROM admin WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
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
    http_response_code(500);
    include_once("error.php");
    die();
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
    $query = <<<QUERY
    SELECT first_name, last_name,
      email, address, home_phone, cell_phone
    FROM customer
    QUERY;
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
    http_response_code(500);
    include_once("error.php");
    die();
  }
}

/**
 * Add a customer to the database
 */
function add_customer($firstname, $lastname, $email, $address, $homePhone, $cellPhone)
{
  try {
    $conn = connect_db();
    $values = [$firstname, $lastname, $email, $address, $homePhone, $cellPhone];
    foreach ($values as $i => $value) {
      $values[$i] = sanitize_sql($conn, $value);
    }
    $fields = "first_name, last_name, email, address, home_phone, cell_phone";
    $query = "INSERT INTO customer ($fields) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", ...$values);
    try {
      $stmt->execute();
    } catch (Exception $e) {
      if ($conn->errno == DUPLICATE_ERROR) {
        return "Customer already existed";
      }
    } finally {
      $stmt->close();
      $conn->close();
    }
    return "";
  } catch (Exception $e) {
    http_response_code(500);
    include_once("error.php");
    die();
  }
}

/**
 * List all products
 */
function list_products()
{
  try {
    $conn = connect_db();
    $query = <<<QUERY
    SELECT id, name, image
    FROM product LEFT OUTER JOIN coffee USING(id)
    ORDER BY category, type, roast_level
    QUERY;
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();
    $stmt->close();
    $conn->close();
    return $products;
  } catch (Exception $e) {
    http_response_code(500);
    include_once("error.php");
    die();
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
    $query = <<<QUERY
    SELECT id, name, image
    FROM product JOIN coffee USING(id)
    ORDER BY type, roast_level
    QUERY;
    if ($category == "brewing-tool") {
      $query = <<<QUERY
      SELECT id, name, image
      FROM product WHERE category = "tool"
      QUERY;
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
    http_response_code(500);
    include_once("error.php");
    die();
  }
}

/**
 * List most visited products
 */
function list_products_by_most_visited($limit = 5)
{
  try {
    $conn = connect_db();
    $query = <<<QUERY
    SELECT id, name, image
    FROM product ORDER BY visited DESC LIMIT ?
    QUERY;
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
    http_response_code(500);
    include_once("error.php");
    die();
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
    $query = <<<QUERY
      SELECT id, name, image
      FROM product WHERE id IN($wildcards)
      QUERY;
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
    http_response_code(500);
    include_once("error.php");
    die();
  }
}

/**
 * Get a product based on product id
 */
function get_product_by_id($id)
{
  try {
    $conn = connect_db();
    $query = <<<QUERY
    SELECT id, name, image, description
    FROM product WHERE id = ?
    QUERY;
    $stmt = $conn->prepare($query);
    $id = sanitize_sql($conn, $id);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
      throw new Exception("Product not found.", 404);
    }
    [$product] = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();
    $stmt->close();
    $conn->close();
    return $product;
  } catch (Exception $e) {
    $errorCode = 500;
    $errorPage = "error.php";
    if ($e->getCode() == 404) {
      $errorCode = 404;
      $errorPage = "404.php";
    }
    http_response_code($errorCode);
    include_once($errorPage);
    die();
  }
}

/**
 * Increment product visited count by one
 */
function update_product_visited_count($id)
{
  try {
    $conn = connect_db();
    $query = <<<QUERY
    UPDATE product
    SET visited = visited + 1
    WHERE id = ?
    QUERY;
    $stmt = $conn->prepare($query);
    $id = sanitize_sql($conn, $id);
    $stmt->bind_param("s", $id);
    $stmt->execute();
  } catch (Exception $e) {
    http_response_code(500);
    include_once("error.php");
    die();
  } finally {
    $stmt->close();
    $conn->close();
  }
}
