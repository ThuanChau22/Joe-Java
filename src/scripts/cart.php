<?php
require_once("../components/cart.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

if (isset($_POST["add_to_cart"]) && isset($_POST["product_id"])) {
  $productId = sanitize_html($_POST["product_id"]);
  if (is_authenticated()) {
    $userId = get_session_user()[UID];
    set_product_to_cart($userId, $productId);
    $number_of_products = get_cart_number_of_products($userId);
  } else {
    set_product_to_cart_session($productId);
    $number_of_products = get_cart_number_of_products_session();
  }
  echo json_encode(["number_of_products" => $number_of_products]);
}

if (isset($_POST["update_to_cart"]) && isset($_POST["product_id"]) && isset($_POST["quantity"])) {
  $productId = sanitize_html($_POST["product_id"]);
  $quantity = sanitize_html($_POST["quantity"]);
  if (is_authenticated()) {
    $userId = get_session_user()[UID];
    set_product_to_cart($userId, $productId, $quantity);
    $number_of_products = get_cart_number_of_products($userId);
  } else {
    set_product_to_cart_session($productId, $quantity);
    $number_of_products = get_cart_number_of_products_session();
  }
  echo json_encode(["number_of_products" => $number_of_products]);
}

if (isset($_POST["remove_from_cart"]) && isset($_POST["product_id"])) {
  $productId = sanitize_html($_POST["product_id"]);
  if (is_authenticated()) {
    $userId = get_session_user()[UID];
    remove_product_from_cart($userId, $productId);
    $cart = list_cart_products($userId);
    $number_of_products = get_cart_number_of_products($userId);
  } else {
    set_product_to_cart_session($productId);
    $cartSession = list_cart_products_session();
    $productIds = $cartSession[PRODUCT_IDS];
    $quantities = $cartSession[QUANTITIES];
    $cart = list_products_by_id($productIds);
    foreach ($cart as $i => $product) {
      $cart[$i]["quantity"] = $quantities[$product["id"]];
    }
    $number_of_products = get_cart_number_of_products_session();
  }
  echo json_encode([
    "html" => cart($cart),
    "number_of_products" => $number_of_products,
  ]);
}
