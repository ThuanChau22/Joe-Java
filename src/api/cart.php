<?php
require_once("../components/cart.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

function add_to_cart($productId)
{
  if (is_authenticated()) {
    $userId = get_session_user()[UID];
    set_product_to_cart($userId, $productId);
    $number_of_products = get_cart_number_of_products($userId);
  } else {
    set_product_to_cart_session($productId);
    $number_of_products = get_cart_number_of_products_session();
  }
  return $number_of_products;
}

function update_to_cart($productId, $quantity)
{
  if (is_authenticated()) {
    $userId = get_session_user()[UID];
    set_product_to_cart($userId, $productId, $quantity);
    $cart = list_cart_products($userId);
    $number_of_products = get_cart_number_of_products($userId);
  } else {
    set_product_to_cart_session($productId, $quantity);
    $cartSession = list_cart_products_session();
    $productIds = $cartSession[PRODUCT_IDS];
    $quantities = $cartSession[QUANTITIES];
    $cart = list_products_by_id($productIds);
    foreach ($cart as $i => $product) {
      $cart[$i]["quantity"] = $quantities[$product["id"]];
    }
    $number_of_products = get_cart_number_of_products_session();
  }
  return [$cart, $number_of_products];
}

function remove_from_cart($productId)
{
  if (is_authenticated()) {
    $userId = get_session_user()[UID];
    remove_product_from_cart($userId, $productId);
    $cart = list_cart_products($userId);
    $number_of_products = get_cart_number_of_products($userId);
  } else {
    remove_product_from_cart_session($productId);
    $cartSession = list_cart_products_session();
    $productIds = $cartSession[PRODUCT_IDS];
    $quantities = $cartSession[QUANTITIES];
    $cart = list_products_by_id($productIds);
    foreach ($cart as $i => $product) {
      $cart[$i]["quantity"] = $quantities[$product["id"]];
    }
    $number_of_products = get_cart_number_of_products_session();
  }
  return [$cart, $number_of_products];
}

try {
  $_POST = json_decode(file_get_contents("php://input"), true);
  if (isset($_POST["add_to_cart"]) && isset($_POST["product_id"])) {
    $productId = sanitize_html($_POST["product_id"]);
    $number_of_products = add_to_cart($productId);
    echo json_encode(["number_of_products" => $number_of_products]);
  }
  if (
    isset($_POST["update_to_cart"])
    && isset($_POST["product_id"])
    && isset($_POST["old_quantity"])
    && isset($_POST["new_quantity"])
  ) {
    $productId = sanitize_html($_POST["product_id"]);
    $oldQuantity = sanitize_html($_POST["old_quantity"]);
    $newQuantity = sanitize_html($_POST["new_quantity"]);
    $isValid = $newQuantity != "" && is_numeric($newQuantity);
    $isValid = $isValid && $oldQuantity != intval($newQuantity);
    if ($isValid && $newQuantity == 0) {
      [$cart, $number_of_products] = remove_from_cart($productId);
    }
    if ($isValid && $newQuantity > 0) {
      $quantity = intval($newQuantity) - $oldQuantity;
      [$cart, $number_of_products] = update_to_cart($productId, $quantity);
    }
    echo json_encode([
      "html" => cart($cart),
      "number_of_products" => $number_of_products,
    ]);
  }
  if (isset($_POST["remove_from_cart"]) && isset($_POST["product_id"])) {
    $productId = sanitize_html($_POST["product_id"]);
    [$cart, $number_of_products] = remove_from_cart($productId);
    echo json_encode([
      "html" => cart($cart),
      "number_of_products" => $number_of_products,
    ]);
  }
} catch (Exception $e) {
  echo json_response(
    code: $e->getCode(),
    message: $e->getMessage(),
  );
}
