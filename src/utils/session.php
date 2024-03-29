<?php
require_once("database.php");

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
  setcookie(session_name(), "", time() - 5 * 24 * 60 * 60);
  session_destroy();
}

/**
 * Set referer
 */
function set_referer($excludes = [])
{
  if (isset($_SERVER["HTTP_REFERER"])) {
    $referer = parse_url($_SERVER["HTTP_REFERER"]);
    $host = $referer["host"];
    if ($host == "localhost") {
      $host .= ":" . $referer["port"];
    }
    $isSameHost = $host == $_SERVER["HTTP_HOST"];
    $isSamePath = $referer["path"] == $_SERVER["REQUEST_URI"];
    $isExcluded = in_array($referer["path"], $excludes);
    if ($isSameHost && !$isSamePath && !$isExcluded) {
      $updated = init_session();
      $_SESSION[USER][REFERER] = $_SERVER["HTTP_REFERER"];
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
function get_user_session()
{
  init_session();
  return $_SESSION[USER];
}

/**
 * Get shopping cart from session
 */
function get_cart_session()
{
  init_session();
  return $_SESSION[SHOPPING_CART];
}

/**
 * Get total quantity of products in cart session
 */
function get_cart_quantity_session()
{
  $number_of_products = 0;
  $cart = get_cart_session();
  foreach ($cart[QUANTITIES] as $quantity) {
    $number_of_products += $quantity;
  }
  return $number_of_products;
}

/**
 * List products from cart session
 */
function list_cart_products_session()
{
  $cartSession = get_cart_session();
  $productIds = $cartSession[PRODUCT_IDS];
  $quantities = $cartSession[QUANTITIES];
  $cart = list_products_by_id($productIds);
  foreach ($cart as $i => $product) {
    $cart[$i]["quantity"] = $quantities[$product["id"]];
  }
  return $cart;
}

/**
 * Set product id and quantity to cart session
 */
function set_product_to_cart_session($productId, $quantity = 1)
{
  $updated = init_session();
  $cart = get_cart_session();
  if (isset($cart[QUANTITIES][$productId])) {
    if ($cart[QUANTITIES][$productId] + $quantity <= 0) {
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
 * Merge cart session to cart database
 */
function merge_to_cart_session($userId)
{
  $cart = get_cart_session();
  merge_to_cart($userId, [
    "productIds" => $cart[PRODUCT_IDS],
    "quantities" => $cart[QUANTITIES],
  ]);
  remove_all_products_from_cart_session();
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
