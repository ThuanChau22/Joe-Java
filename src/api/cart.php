<?php
require_once("../components/cart.php");
require_once("../utils/database.php");
require_once("../utils/session.php");
require_once("../utils/utils.php");

function add_to_cart($productId)
{
  if (is_authenticated()) {
    $userId = get_user_session()[UID];
    set_product_to_cart($userId, $productId);
    $numberOfProducts = get_cart_quantities($userId);
  } else {
    set_product_to_cart_session($productId);
    $numberOfProducts = get_cart_quantities_session();
  }
  return $numberOfProducts;
}

function update_to_cart($productId, $quantity)
{
  if (is_authenticated()) {
    $userId = get_user_session()[UID];
    set_product_to_cart($userId, $productId, $quantity);
    $cart = list_cart_products($userId);
    $numberOfProducts = get_cart_quantities($userId);
  } else {
    set_product_to_cart_session($productId, $quantity);
    $cart = list_cart_products_session();
    $numberOfProducts = get_cart_quantities_session();
  }
  return [$cart, $numberOfProducts];
}

function remove_from_cart($productId)
{
  if (is_authenticated()) {
    $userId = get_user_session()[UID];
    remove_product_from_cart($userId, $productId);
    $cart = list_cart_products($userId);
    $numberOfProducts = get_cart_quantities($userId);
  } else {
    remove_product_from_cart_session($productId);
    $cart = list_cart_products_session();
    $numberOfProducts = get_cart_quantities_session();
  }
  return [$cart, $numberOfProducts];
}

try {
  $_POST = json_decode(file_get_contents("php://input"), true);
  if (isset($_POST["add_to_cart"]) && isset($_POST["product_id"])) {
    $productId = sanitize_html($_POST["product_id"]);
    $numberOfProducts = add_to_cart($productId);
    echo json_encode([
      "number_of_products" => $numberOfProducts,
    ]);
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
      [$cart, $numberOfProducts] = remove_from_cart($productId);
    }
    if ($isValid && $newQuantity > 0) {
      $quantity = intval($newQuantity) - $oldQuantity;
      [$cart, $numberOfProducts] = update_to_cart($productId, $quantity);
    }
    echo json_encode([
      "html" => cart($cart),
      "number_of_products" => $numberOfProducts,
    ]);
  }
  if (isset($_POST["remove_from_cart"]) && isset($_POST["product_id"])) {
    $productId = sanitize_html($_POST["product_id"]);
    [$cart, $numberOfProducts] = remove_from_cart($productId);
    echo json_encode([
      "html" => cart($cart),
      "number_of_products" => $numberOfProducts,
    ]);
  }
} catch (Exception $e) {
  echo json_response(
    code: $e->getCode(),
    message: $e->getMessage(),
  );
}
