<?php
require_once("../../vendor/autoload.php");

/**
 * Cookie variables
 */
define("COOKIE_LIMIT", 15 * 24 * 60 * 60);
define("VISITED_PRODUCTS", "VISITED_PRODUCTS");

/**
 * Session variables
 */
define("USER", "user");
define("UID", "uid");
define("IS_ADMIN", "isAdmin");
define("REFERER", "referer");
define("SHOPPING_CART", "SHOPPING_CART");
define("PRODUCT_IDS", "productIds");
define("QUANTITIES", "quantities");
define("CHECK", "check");

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
 * Initialize session
 * Return update validation function
 */
function init_session()
{
  if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
  }
  if (!isset($_SESSION["init"])) {
    session_regenerate_id();
    $_SESSION["init"] = true;
    $_SESSION[USER] = [];
    $_SESSION[SHOPPING_CART] = [
      PRODUCT_IDS => [],
      QUANTITIES => [],
    ];
    $_SESSION[CHECK] = "";
  }
  return function () {
    $check = json_encode($_SESSION[USER]);
    $check .= json_encode($_SESSION[SHOPPING_CART]);
    if (isset($_SERVER["HTTP_USER_AGENT"])) {
      $check .= $_SERVER["HTTP_USER_AGENT"];
    }
    $_SESSION[CHECK] = hash("sha512", $check);
  };
}

/**
 * Remove current session
 */
function remove_session()
{
  init_session();
  unset($_SESSION);
  setcookie(session_name(), "", time() - COOKIE_LIMIT);
  session_destroy();
}

/**
 * Set referer
 */
function set_referer($excludes = [])
{
  if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = parse_url($_SERVER['HTTP_REFERER']);
    $host = $referer["host"];
    if ($host == "localhost") {
      $host .= ":" . $referer["port"];
    }
    $isSameHost = $host == $_SERVER['HTTP_HOST'];
    $isSamePath = $referer["path"] == $_SERVER['REQUEST_URI'];
    $isExcluded = in_array($referer["path"], $excludes);
    if ($isSameHost && !$isSamePath && !$isExcluded) {
      $updated = init_session();
      $_SESSION[USER][REFERER] = $_SERVER['HTTP_REFERER'];
      $updated();
    }
  }
}

/**
 * Remove and return referer
 */
function pop_referer()
{
  $updated = init_session();
  $referer = "/home";
  if (isset($_SESSION[USER][REFERER])) {
    $referer = $_SESSION[USER][REFERER];
    unset($_SESSION[USER][REFERER]);
    $updated();
  }
  return $referer;
}

/**
 * Set user as authenticated
 */
function set_authenticated($uid, $isAdmin)
{
  $updated = init_session();
  $_SESSION[USER][UID] = $uid;
  $_SESSION[USER][IS_ADMIN] = $isAdmin;
  $updated();
}

/**
 * Check whether user is authenticated
 */
function is_authenticated()
{
  init_session();
  $user = $_SESSION[USER];
  $check = json_encode($user);
  $check .= json_encode($_SESSION[SHOPPING_CART]);
  if (isset($_SERVER["HTTP_USER_AGENT"])) {
    $check .= $_SERVER["HTTP_USER_AGENT"];
  }
  if ($_SESSION[CHECK] != hash("sha512", $check)) {
    return false;
  }
  return isset($user[UID]) && isset($user[IS_ADMIN]);
}

/**
 * Check whether user is an admin
 */
function is_admin()
{
  return is_authenticated() && $_SESSION[USER][IS_ADMIN];
}

/**
 * Get current user information
 */
function get_session_user()
{
  init_session();
  return $_SESSION[USER];
}

/**
 * List shopping cart products from cart session
 */
function list_cart_products_session()
{
  init_session();
  return $_SESSION[SHOPPING_CART];
}

/**
 * Get total quantity of products in cart session
 */
function get_cart_number_of_products_session()
{
  $number_of_products = 0;
  $cart = list_cart_products_session();
  foreach ($cart[QUANTITIES] as $quantity) {
    $number_of_products += $quantity;
  }
  return $number_of_products;
}

/**
 * Set product id and quantity to cart session
 */
function set_product_to_cart_session($productId, $quantity = 1)
{
  $updated = init_session();
  $cart = list_cart_products_session();
  if (isset($cart[QUANTITIES][$productId])) {
    if($cart[QUANTITIES][$productId] + $quantity <= 0) {
      throw new Exception("Quantity cannot be less than 1", 400);
    }
    $_SESSION[SHOPPING_CART][QUANTITIES][$productId] += $quantity;
  } else {
    $_SESSION[SHOPPING_CART][PRODUCT_IDS][] = $productId;
    $_SESSION[SHOPPING_CART][QUANTITIES][$productId] = $quantity;
  }
  $updated();
}

/**
 * Remove a product from cart session
 */
function remove_product_from_cart_session($productId)
{
  $updated = init_session();
  $productIds = $_SESSION[SHOPPING_CART][PRODUCT_IDS];
  $productIdIndex = -1;
  for ($i = 0; $i < count($productIds); $i++) {
    if ($productIds[$i] == $productId) {
      $productIdIndex = $i;
      break;
    }
  }
  if ($productIdIndex != -1) {
    array_splice($_SESSION[SHOPPING_CART][PRODUCT_IDS], $productIdIndex, 1);
  }
  unset($_SESSION[SHOPPING_CART][QUANTITIES][$productId]);
  $updated();
}

/**
 * Clear shopping cart products from session
 */
function remove_all_products_from_cart_session()
{
  $updated = init_session();
  $_SESSION[SHOPPING_CART] = [
    PRODUCT_IDS => [],
    QUANTITIES => [],
  ];
  $updated();
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
