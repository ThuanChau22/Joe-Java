<?php
require_once("../../vendor/autoload.php");
$dotenvFilePath = dirname(__DIR__, 2);
$dotenv = Dotenv\Dotenv::createImmutable($dotenvFilePath);
$dotenv->safeLoad();

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
