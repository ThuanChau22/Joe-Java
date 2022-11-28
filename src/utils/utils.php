<?php
require_once("../../vendor/autoload.php");

/**
 * Cookie names
 */
define("VISITED_PRODUCTS", "visited-products");

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
function set_visited_product_id($id)
{
  $productIdList = list_visited_product_id();
  $productIdListLength = count($productIdList);
  $productIdIndex = -1;
  for ($i = 0; $i < $productIdListLength && $i != $productIdIndex; $i++) {
    if ($productIdList[$i] == $id) {
      $productIdIndex = $i;
    }
  }
  if ($productIdIndex != -1) {
    array_splice($productIdList, $productIdIndex, 1);
  }
  if ($productIdListLength == 5) {
    array_pop($productIdList);
  }
  array_unshift($productIdList, $id);
  setcookie(
    VISITED_PRODUCTS,
    value: json_encode($productIdList),
    expires_or_options: time() + 5 * 24 * 60 * 60,
    secure: true,
    httponly: true,
  );
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
