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
function connectDB()
{
  try {
    load_ENV();
    $host = $_ENV["DB_HOST"];
    $user = $_ENV["DB_USER"];
    $pass = $_ENV["DB_PASS"];
    $dbname = $_ENV["DB_NAME"];
    return new mysqli($host, $user, $pass, $dbname);
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

/**
 * Convert query results into a list
 * Each entry is an associative array
 */
function toArray($result, $fields)
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
function toMap($result, $fields)
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
function sanitizeMySQL($conn, $string)
{
  return $conn->real_escape_string($string);
}

/**
 * Authenticate user with password
 */
function login($username, $password)
{
  try {
    $conn = connectDB();
    $username = sanitizeMySQL($conn, $username);
    $password = sanitizeMySQL($conn, $password);
    $query = "SELECT password FROM admin WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $numberOfRows = $result->num_rows;
    $user = $result->fetch_assoc();
    $result->close();
    $stmt->close();
    $conn->close();
    $hashedPassword = hash("sha512", $password);
    $isAuth = $numberOfRows == 1 && $user["password"] == $hashedPassword;
    return $isAuth ? "" : "Incorrect user name or password.";
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

/**
 * List all customers
 * If search is specified,
 * Return customers based on search term
 */
function listCustomers($search = null)
{
  try {
    $conn = connectDB();
    $conditions = "";
    $inputs = [];
    if (isset($search)) {
      $search = sanitizeMySQL($conn, $search);
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
    $customers = toArray($result, $fields);
    $result->close();
    $stmt->close();
    $conn->close();
    return $customers;
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

/**
 * Add a customer to the database
 */
function addCustomer($firstname, $lastname, $email, $address, $homePhone, $cellPhone)
{
  try {
    $conn = connectDB();
    $inputs = [$firstname, $lastname, $email, $address, $homePhone, $cellPhone];
    for ($i = 0; $i < count($inputs); $i++) {
      $inputs[$i] = sanitizeMySQL($conn, $inputs[$i]);
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
    die(header('Location: ./error'));
  }
}

/**
 * List all products
 */
function listProducts()
{
  try {
    $conn = connectDB();
    $query = <<<QUERY
    SELECT id, name, image
    FROM product LEFT OUTER JOIN coffee USING(id)
    ORDER BY category, type, roast_level
    QUERY;
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = toArray($result, ["id", "name", "image"]);
    $result->close();
    $stmt->close();
    $conn->close();
    return $products;
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

/**
 * List products based on category
 * Category options: coffee, brewing-tool
 */
function listProductsByCategory($category = "coffee")
{
  try {
    $conn = connectDB();
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
    $products = toArray($result, ["id", "name", "image"]);
    $result->close();
    $stmt->close();
    $conn->close();
    return $products;
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

/**
 * List most visited products
 */
function listProductsByMostVisited($limit = 5)
{
  try {
    $conn = connectDB();
    $query = <<<QUERY
    SELECT id, name, image
    FROM product ORDER BY visited DESC LIMIT ?
    QUERY;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = toArray($result, ["id", "name", "image"]);
    $result->close();
    $stmt->close();
    $conn->close();
    return $products;
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

/**
 * List products from a list of ids
 */
function listProductsByIds($ids)
{
  try {
    $conn = connectDB();
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
    $productsMap = toMap($result, ["id", "name", "image"]);
    $result->close();
    $stmt->close();
    $conn->close();
    $products = [];
    foreach ($ids as $id) {
      $products[] = $productsMap[$id];
    }
    return $products;
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

/**
 * Get a product based on product id
 */
function getProductById($id)
{
  try {
    $conn = connectDB();
    $fields = "id, name, image, description";
    $query = "SELECT $fields FROM product WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fields = array_map("trim", explode(",", $fields));
    [$product] = toArray($result, $fields);
    $result->close();
    $stmt->close();
    $conn->close();
    return $product;
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

/**
 * Increment product visited count by one
 */
function increaseProductVisitedCount($id)
{
  try {
    $conn = connectDB();
    $query = "UPDATE product SET visited = visited + 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    return "";
  } catch (Exception $e) {
    die(header('Location: ./error'));
  } finally {
    $stmt->close();
    $conn->close();
  }
}
