<?php
require_once("utils.php");

// Connect to database
// Return connection instance
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

// Sanitize MySQL input
function sanitizeMySQL($conn, $string)
{
  return $conn->real_escape_string($string);
}

// Authenticate user with password
function login($username, $password)
{
  try {
    $conn = connectDB();
    $username = sanitizeMySQL($conn, $username);
    $password = sanitizeMySQL($conn, $password);
    $stmt = $conn->prepare("SELECT password FROM admin WHERE username = ?");
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

// List customers
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
          $conditions .= "$key LIKE ?";
          $conditions .= $i != count($keys) - 1 ? " OR " : "";
          $inputs[] = "%$search%";
        }
      }
    }
    $fields = "first_name, last_name, email, address, home_phone, cell_phone";
    $statement = "SELECT $fields FROM customer";
    if ($conditions != "") {
      $statement .= " WHERE $conditions";
    }
    $stmt = $conn->prepare($statement);
    if ($conditions != "") {
      $stmt->bind_param("sssss", ...$inputs);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $customers = [];
    foreach ($result as $row) {
      $customer = [];
      foreach (explode(",", $fields) as $field) {
        $field = trim($field);
        $customer[$field] = $row[$field];
      }
      $customers[] = $customer;
    }
    $result->close();
    $stmt->close();
    $conn->close();
    return $customers;
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

// Add a customer to the database
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
      $stmt = $conn->prepare("INSERT INTO customer ($fields) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssss", ...$inputs);
      $stmt->execute();
      return "";
    } catch (Exception $e) {
      if ($conn->errno == 1062) {
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
