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

/**
 * Set referer
 */
function setReferer($excludes = [])
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
      if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
      }
      $_SESSION["referer"] = $_SERVER['HTTP_REFERER'];
    }
  }
}

/**
 * Remove and return referer
 */
function popReferer()
{
  if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
  }
  $referer = "/home";
  if (isset($_SESSION["referer"])) {
    $referer = $_SESSION["referer"];
    unset($_SESSION["referer"]);
  }
  return $referer;
}

/**
 * Create a new session
 */
function create_session($user, $isAdmin = false)
{
  if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
  }
  if (!isset($_SESSION["init"])) {
    session_regenerate_id();
    $_SESSION["init"] = true;
  }
  $_SESSION["user"] = $user;
  $_SESSION["isAdmin"] = $isAdmin;
  $_SESSION["check"] = hash("sha512", $user . $_SERVER["HTTP_USER_AGENT"]);
}

/**
 * Check whether session existed
 * and valid with current user
 */
function valid_session()
{
  if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
  }
  $user = isset($_SESSION["user"]) ? $_SESSION["user"] : "";
  $check = isset($_SESSION["check"]) ? $_SESSION["check"] : "";
  $userAgent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
  if ($user != "" && $check != "" && $check == hash("sha512", $user . $userAgent)) {
    return true;
  }
  return false;
}

/**
 * Remove current session
 */
function remove_session()
{
  if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
  }
  unset($_SESSION);
  setcookie(session_name(), "", time() - 3 * 24 * 60 * 60);
  session_destroy();
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
