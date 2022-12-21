<?php
require_once("../../vendor/autoload.php");

/**
 * Cookie variables
 */
define("COOKIE_LIMIT", 15 * 24 * 60 * 60);
define("VISITED_PRODUCTS", "VISITED_PRODUCTS");

/**
 * Load environment variables from .env to _ENV
 */
function load_env()
{
  $dotenvFilePath = dirname(__DIR__, 2);
  $dotenv = Dotenv\Dotenv::createImmutable($dotenvFilePath);
  $dotenv->safeLoad();
}

/**
 * Log output to browser
 */
function console_log($data)
{
  $data = json_encode($data);
  if (is_array($data) || is_object($data)) {
    $data = "JSON.parse($data)";
  }
  echo "<script>console.log($data)</script>";
}

/**
 * Sanitize user input
 */
function sanitize_html($string)
{
  return htmlentities(trim($string));
}

/**
 * Stylizing 10-digit phone number
 */
function pretty_phone_number($phoneNumber)
{
  $area = substr($phoneNumber, 0, 3);
  $prefix = substr($phoneNumber, 3, 3);
  $line = substr($phoneNumber, 7, 4);
  return "($area) $prefix-$line";
}

/**
 * List visited product ids from cookie
 */
function list_visited_product_id()
{
  $productIdList = [];
  if (isset($_COOKIE[VISITED_PRODUCTS])) {
    $productIdList = json_decode($_COOKIE[VISITED_PRODUCTS]);
  }
  return $productIdList;
}

/**
 * Set product id as first position to cookie
 */
function set_visited_product_id($productId)
{
  $productIdList = list_visited_product_id();
  $productIdListLength = count($productIdList);
  $productIdIndex = -1;
  for ($i = 0; $i < $productIdListLength && $i != $productIdIndex; $i++) {
    if ($productIdList[$i] == $productId) {
      $productIdIndex = $i;
    }
  }
  if ($productIdIndex != -1) {
    array_splice($productIdList, $productIdIndex, 1);
  }
  if ($productIdListLength == 5) {
    array_pop($productIdList);
  }
  array_unshift($productIdList, $productId);
  setcookie(
    VISITED_PRODUCTS,
    value: json_encode($productIdList),
    expires_or_options: time() + COOKIE_LIMIT,
    secure: true,
    httponly: true,
  );
}

/**
 * Handle client side exception
 */
function handle_client_error($exception)
{
  // console_log($exception->getMessage());
  $code = $exception->getCode();
  http_response_code($code);
  include_once($code == 404 ? "404.php" : "error.php");
  die();
}

/**
 * Handle json response
 */
function json_response($data = null, $code = 200, $message = "")
{
  header_remove();
  http_response_code($code);
  header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
  header('Content-Type: application/json');
  $body = $data;
  if ($code >= 300 && $code < 500) {
    $body = [
      "status" => $code,
      "message" => $message,
    ];
  }
  if ($code >= 500) {
    $body = [
      "status" => $code,
      "message" => "Internal Server Error",
    ];
  }
  return json_encode($body);
}
