<?php
require_once("../../vendor/autoload.php");

/**
 * Cookie names
 */
define("VISITED_PRODUCTS", "visited-products");

/**
 * Load environment variables from .env to _ENV
 */
function load_ENV()
{
  $dotenvFilePath = dirname(__DIR__, 2);
  $dotenv = Dotenv\Dotenv::createImmutable($dotenvFilePath);
  $dotenv->safeLoad();
}

/**
 * Log to browser
 */
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

/**
 * Sanitize user input
 */
function sanitizeHTML($string)
{
  return htmlentities(trim($string));
}

/**
 * List product ids from cookie
 */
function getCookieProducts()
{
  $productIds = [];
  if (isset($_COOKIE[VISITED_PRODUCTS])) {
    $productIds = json_decode($_COOKIE[VISITED_PRODUCTS]);
  }
  return $productIds;
}

/**
 * Add product id into cookie
 */
function addCookieProduct($id)
{
  $productIds = getCookieProducts();
  $index = -1;
  for ($i = 0; $i < count($productIds) && $i != $index; $i++) {
    if ($productIds[$i] == $id) {
      $index = $i;
    }
  }
  if ($index != -1) {
    array_splice($productIds, $index, 1);
  }
  if (count($productIds) == 5) {
    array_pop($productIds);
  }
  array_unshift($productIds, $id);
  $expireTime = time() + 5 * 24 * 60 * 60;
  setcookie(VISITED_PRODUCTS, json_encode($productIds), $expireTime);
}

/**
 * Stylizing 10-digit phone number
 */
function prettyPhoneNumber($phoneNumber)
{
  $area = substr($phoneNumber, 0, 3);
  $prefix = substr($phoneNumber, 3, 3);
  $line = substr($phoneNumber, 7, 4);
  return "($area) $prefix-$line";
}