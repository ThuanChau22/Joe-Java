<?php
require_once("../../vendor/autoload.php");

// Load environment variables from .env to _ENV
function load_ENV()
{
  $dotenvFilePath = dirname(__DIR__, 2);
  $dotenv = Dotenv\Dotenv::createImmutable($dotenvFilePath);
  $dotenv->safeLoad();
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

// Sanitize user input
function sanitizeHTML($string)
{
  return htmlentities(trim($string));
}
