<?php
require_once("../../vendor/autoload.php");

// Load environment variables from .env to _ENV
$dotenvFilePath = dirname(__DIR__, 2);
$dotenv = Dotenv\Dotenv::createImmutable($dotenvFilePath);
$dotenv->safeLoad();

// Connect to database
// Return connection instance
function connectDB()
{
  try {
    $host = $_ENV["DB_HOST"];
    $user = $_ENV["DB_USER"];
    $pass = $_ENV["DB_PASS"];
    $dbname = $_ENV["DB_NAME"];
    return new mysqli($host, $user, $pass, $dbname);
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

// Log into browser
function consoleLog($data)
{
  if (is_string($data)) {
    $data = "'$data'";
  }
  if (is_array($data) || is_object($data)) {
    $data = "JSON.parse('" . json_encode($data) . "')";
  }
  echo "<script>console.log($data)</script>";
}
