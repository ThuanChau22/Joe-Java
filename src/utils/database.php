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
 * Convert query results into a list
 * Each entry is an associative array
 */
function to_array($result, $fields)
{
  $array = [];
  foreach ($result as $row) {
    $entry = [];
    foreach ($fields as $field) {
      $entry[$field] = $row[$field];
    }
    $array[] = $entry;
  }
  return $array;
}

/**
 * Convert query results into a map
 * Each entry has record id as key
 * and an associative array as value
 * * Order of entries is not guaranteed
 */
function to_map($result, $fields)
{
  $map = [];
  foreach ($result as $row) {
    $entry = [];
    foreach ($fields as $field) {
      $entry[$field] = $row[$field];
    }
    $map[$row["id"]] = $entry;
  }
  return $map;
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
    $users = to_array($result, ["password"]);
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
    $conditions = "";
    $inputs = [];
    if ($search != "") {
      $search = sanitize_sql($conn, $search);
      if ($search != "") {
        $keys = ["first_name", "last_name", "email", "home_phone", "cell_phone"];
        foreach ($keys as $i => $key) {
          $isLast = $i == count($keys) - 1;
          $conditions .= "$key LIKE ?" . ($isLast ? "" : " OR ");
          $inputs[] = "%$search%";
        }
      }
    }
    $fields = "first_name, last_name, email, address, home_phone, cell_phone";
    $query = "SELECT $fields FROM customer";
    $stmt = $conn->prepare($query);
    if ($conditions != "") {
      $stmt = $conn->prepare("$query WHERE $conditions");
      $stmt->bind_param("sssss", ...$inputs);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $fields = array_map("trim", explode(",", $fields));
    $customers = to_array($result, $fields);
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
    $inputs = [$firstname, $lastname, $email, $address, $homePhone, $cellPhone];
    for ($i = 0; $i < count($inputs); $i++) {
      $inputs[$i] = sanitize_sql($conn, $inputs[$i]);
    }
    try {
      $fields = "first_name, last_name, email, address, home_phone, cell_phone";
      $query = "INSERT INTO customer ($fields) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("ssssss", ...$inputs);
      $stmt->execute();
      return "";
    } catch (Exception $e) {
      if ($conn->errno == DUPLICATE_ERROR) {
        return "Customer already existed";
      }
    } finally {
      $stmt->close();
      $conn->close();
    }
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
    $products = to_array($result, ["id", "name", "image"]);
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
    $products = to_array($result, ["id", "name", "image"]);
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
    $products = to_array($result, ["id", "name", "image"]);
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
function list_products_by_id($ids)
{
  try {
    $products = [];
    if (count($ids) > 0) {
      $conn = connect_db();
      $wildcards = $types = "";
      for ($i = 0; $i < count($ids); $i++) {
        $isLast = $i == count($ids) - 1;
        $wildcards .= "?" . ($isLast ? "" : ",");
        $types .= "s";
      }
      $query = <<<QUERY
      SELECT id, name, image
      FROM product WHERE id IN($wildcards)
      QUERY;
      $stmt = $conn->prepare($query);
      $stmt->bind_param($types, ...$ids);
      $stmt->execute();
      $result = $stmt->get_result();
      $productsMap = to_map($result, ["id", "name", "image"]);
      $result->close();
      $stmt->close();
      $conn->close();
      foreach ($ids as $id) {
        $products[] = $productsMap[$id];
      }
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
    $fields = "id, name, image, description";
    $query = "SELECT $fields FROM product WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
      throw new Exception("Product not found.", 404);
    }
    $fields = array_map("trim", explode(",", $fields));
    [$product] = to_array($result, $fields);
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
    $query = "UPDATE product SET visited = visited + 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
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
