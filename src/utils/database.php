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

function close_db($conn = null, $stmt = null, $result = null)
{
  if (isset($result)) {
    $result->close();
  }
  if (isset($result)) {
    $stmt->close();
  }
  if (isset($conn)) {
    $conn->close();
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
 * Return user data if authenticated
 * else return error message
 */
function login($email, $password)
{
  $conn = $stmt = $result = null;
  try {
    $conn = connect_db();
    $email = sanitize_sql($conn, $email);
    $password = sanitize_sql($conn, $password);
    $query = "SELECT id, password FROM user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $hashedPassword = hash("sha512", $password);
    if (count($users) == 0 || $users[0]["password"] != $hashedPassword) {
      return "Incorrect user name or password";
    }
    $query = "SELECT id FROM admin WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $users[0]["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    return [
      "id" => $users[0]["id"],
      "isAdmin" => $result->num_rows == 1,
    ];
  } catch (Exception $e) {
    throw $e;
  } finally {
    close_db($conn, $stmt, $result);
  }
}

/**
 * List all customers
 * If search is specified,
 * Return customers based on search term
 */
function list_customers($search = "")
{
  $conn = $stmt = $result = null;
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
    return $result->fetch_all(MYSQLI_ASSOC);
  } catch (Exception $e) {
    throw $e;
  } finally {
    close_db($conn, $stmt, $result);
  }
}

/**
 * Add a customer to the database
 * $field: {
 *    email, password,
 *    first_name, last_name,
 *    home_phone, cell_phone, address
 * }
 */
function add_customer($fields)
{
  $conn = $stmt = null;
  try {
    $conn = connect_db();
    $email = sanitize_sql($conn, $fields["email"]);
    $password = sanitize_sql($conn, $fields["password"]);
    $values = [
      $fields["first_name"], $fields["last_name"],
      $fields["home_phone"], $fields["cell_phone"], $fields["address"],
    ];
    foreach ($values as $i => $value) {
      $values[$i] = sanitize_sql($conn, $value);
    }

    $conn->begin_transaction();
    // Insert into user
    $query = "INSERT INTO user (email, password) VALUES (LOWER(?), ?)";
    $stmt = $conn->prepare($query);
    $hashedPassword = hash("sha512", $password);
    $stmt->bind_param("ss", $email, $hashedPassword);
    $stmt->execute();

    // Insert into customer
    $query = <<<SQL
    INSERT INTO customer
    (id, first_name, last_name, home_phone, cell_phone, address)
     VALUES (LAST_INSERT_ID(), ?, ?, ?, ?, ?)
    SQL;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", ...$values);
    $stmt->execute();

    $conn->commit();
    return "";
  } catch (Exception $e) {
    if (isset($conn)) {
      $conn->rollback();
    }
    if ($e->getCode() == DUPLICATE_ERROR) {
      return "Email already existed";
    }
    throw $e;
  } finally {
    close_db($conn, $stmt);
  }
}

/**
 * List all products
 */
function list_products()
{
  $conn = $stmt = $result = null;
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
    return $result->fetch_all(MYSQLI_ASSOC);
  } catch (Exception $e) {
    throw $e;
  } finally {
    close_db($conn, $stmt, $result);
  }
}

/**
 * List products based on category
 * Category options: coffee, brewing-tool
 */
function list_products_by_category($category = "coffee")
{
  $conn = $stmt = $result = null;
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
    return $products;
  } catch (Exception $e) {
    throw $e;
  } finally {
    close_db($conn, $stmt, $result);
  }
}

/**
 * List most visited products
 */
function list_products_by_most_visited($limit = 5)
{
  $conn = $stmt = $result = null;
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
    return $result->fetch_all(MYSQLI_ASSOC);
  } catch (Exception $e) {
    throw $e;
  } finally {
    close_db($conn, $stmt, $result);
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
  $conn = $stmt = $result = null;
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
  } finally {
    close_db($conn, $stmt, $result);
  }
}

/**
 * Get a product based on product id
 */
function get_product_by_id($productId)
{
  $conn = $stmt = $result = null;
  try {
    $conn = connect_db();
    $query = <<<SQL
    SELECT id, name, image, price, description
    FROM product WHERE id = ?
    SQL;
    $stmt = $conn->prepare($query);
    $productId = sanitize_sql($conn, $productId);
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
      throw new Exception("Product not found", 404);
    }
    return $result->fetch_all(MYSQLI_ASSOC)[0];
  } catch (Exception $e) {
    throw $e;
  } finally {
    close_db($conn, $stmt, $result);
  }
}

/**
 * Increment product visited count by one
 */
function update_product_visited_count($productId)
{
  $conn = $stmt = null;
  try {
    $conn = connect_db();
    $productId = sanitize_sql($conn, $productId);
    $query = <<<SQL
    UPDATE product
    SET visited = visited + 1
    WHERE id = ?
    SQL;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $productId);
    $stmt->execute();
  } catch (Exception $e) {
    throw $e;
  } finally {
    close_db($conn, $stmt);
  }
}

/**
 * Get cart update timestamp
 */
function get_cart_update($userId)
{
  $conn = $stmt = $result = null;
  try {
    $conn = connect_db();
    $userId = sanitize_sql($conn, $userId);
    $query = "SELECT cart_update FROM user WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $timestamp = $result->fetch_all(MYSQLI_ASSOC)[0]["cart_update"];
    return date(DATE_ATOM, strtotime($timestamp));
  } catch (Exception $e) {
    throw $e;
  } finally {
    close_db($conn, $stmt, $result);
  }
}

/**
 * Get total quantity of products in cart
 */
function get_cart_quantities($userId)
{
  $conn = $stmt = $result = null;
  try {
    $conn = connect_db();
    $userId = sanitize_sql($conn, $userId);
    $query = <<<SQL
    SELECT SUM(quantity) AS count
    FROM cart WHERE user_id = ?
    SQL;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_all(MYSQLI_ASSOC)[0]["count"];
    return $count == null ? 0 : $count;
  } catch (Exception $e) {
    throw $e;
  } finally {
    close_db($conn, $stmt, $result);
  }
}

/**
 * List product from customer cart
 */
function list_cart_products($userId)
{
  $conn = $stmt = $result = null;
  try {
    $conn = connect_db();
    $userId = sanitize_sql($conn, $userId);
    $query = <<<SQL
    SELECT id, name, image, price, quantity
    FROM cart INNER JOIN product ON id = product_id
    WHERE user_id = ? ORDER BY cart.create_at
    SQL;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
  } catch (Exception $e) {
    throw $e;
  } finally {
    close_db($conn, $stmt, $result);
  }
}

/**
 * Set product id to customer cart
 */
function set_product_to_cart($userId, $productId, $quantity = 1)
{
  $conn = $stmt = null;
  try {
    $conn = connect_db();
    $userId = sanitize_sql($conn, $userId);
    $productId = sanitize_sql($conn, $productId);
    $quantity = sanitize_sql($conn, $quantity);

    $conn->begin_transaction();
    // Insert/Update product to customer cart
    $query = <<<SQL
    INSERT INTO cart
    (user_id, product_id, quantity) VALUES (?, ?, 1)
    ON DUPLICATE KEY UPDATE quantity = quantity + ?
    SQL;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $userId, $productId, $quantity);
    $stmt->execute();

    // Update customer cart timestamp
    $query = "UPDATE user SET cart_update = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();

    $conn->commit();
  } catch (Exception $e) {
    if (isset($conn)) {
      $conn->rollback();
    }
    throw $e;
  } finally {
    close_db($conn, $stmt);
  }
}

/**
 * Remove a product from cart
 */
function remove_product_from_cart($userId, $productId)
{
  $conn = $stmt = null;
  try {
    $conn = connect_db();
    $userId = sanitize_sql($conn, $userId);
    $productId = sanitize_sql($conn, $productId);

    $conn->begin_transaction();
    // Remove product from customer cart
    $query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $userId, $productId);
    $stmt->execute();

    // Update customer cart timestamp
    $query = "UPDATE user SET cart_update = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();

    $conn->commit();
  } catch (Exception $e) {
    if (isset($conn)) {
      $conn->rollback();
    }
    throw $e;
  } finally {
    close_db($conn, $stmt);
  }
}

/**
 * Clear products from customer cart
 */
function remove_all_products_from_cart($userId)
{
  $conn = $stmt = null;
  try {
    $conn = connect_db();
    $userId = sanitize_sql($conn, $userId);

    $conn->begin_transaction();
    // Remove all products from customer cart
    $query = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();

    // Update customer cart timestamp
    $query = "UPDATE user SET cart_update = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();

    $conn->commit();
  } catch (Exception $e) {
    if (isset($conn)) {
      $conn->rollback();
    }
    throw $e;
  } finally {
    close_db($conn, $stmt);
  }
}
