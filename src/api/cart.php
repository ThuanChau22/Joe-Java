<?php
require_once("../components/cart.php");
require_once("../utils/database.php");
require_once("../utils/session.php");
require_once("../utils/utils.php");

function update_to_cart($productId, $quantity)
{
  if (is_authenticated()) {
    $userId = get_user_session()[UID];
    set_product_to_cart($userId, $productId, $quantity);
    $cart = list_cart_products($userId);
    $numberOfProducts = get_cart_quantity($userId);
  } else {
    set_product_to_cart_session($productId, $quantity);
    $cart = list_cart_products_session();
    $numberOfProducts = get_cart_quantity_session();
  }
  return [$cart, $numberOfProducts];
}

function remove_from_cart($productId)
{
  if (is_authenticated()) {
    $userId = get_user_session()[UID];
    remove_product_from_cart($userId, $productId);
    $cart = list_cart_products($userId);
    $numberOfProducts = get_cart_quantity($userId);
  } else {
    remove_product_from_cart_session($productId);
    $cart = list_cart_products_session();
    $numberOfProducts = get_cart_quantity_session();
  }
  return [$cart, $numberOfProducts];
}

try {
  $requestMethod = $_SERVER["REQUEST_METHOD"];
  if ($requestMethod == "GET") {
    if (isset($_GET["quantity"])) {
      if (is_authenticated()) {
        $userId = get_user_session()[UID];
        $numberOfProducts = get_cart_quantity($userId);
      } else {
        $numberOfProducts = get_cart_quantity_session();
      }
      echo json_response($numberOfProducts);
    } else {
      if (is_authenticated()) {
        $userId = get_user_session()[UID];
        $cart = list_cart_products($userId);
      } else {
        $cart = list_cart_products_session();
      }
      if (isset($_GET["html"])) {
        $cart = cart($cart);
      }
      echo json_response($cart);
    }
  }
  if ($requestMethod == "POST") {
    $_POST = json_decode(file_get_contents("php://input"), true);
    if (isset($_POST["add_to_cart"]) && isset($_POST["product_id"])) {
      $productId = sanitize_html($_POST["product_id"]);
      if (is_authenticated()) {
        $userId = get_user_session()[UID];
        set_product_to_cart($userId, $productId);
      } else {
        set_product_to_cart_session($productId);
      }
      echo json_response();
    }

    $isQuantityZero = false;
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
      if ($isValid && $newQuantity > 0) {
        $quantity = intval($newQuantity) - $oldQuantity;
        if (is_authenticated()) {
          $userId = get_user_session()[UID];
          set_product_to_cart($userId, $productId, $quantity);
        } else {
          set_product_to_cart_session($productId, $quantity);
        }
        echo json_response();
      }
      if ($isValid && $newQuantity == 0) {
        $isQuantityZero = true;
      }
    }
    if (
      isset($_POST["remove_from_cart"])
      && isset($_POST["product_id"])
      || $isQuantityZero
    ) {
      $productId = sanitize_html($_POST["product_id"]);
      if (is_authenticated()) {
        $userId = get_user_session()[UID];
        remove_product_from_cart($userId, $productId);
      } else {
        remove_product_from_cart_session($productId);
      }
      echo json_response();
    }
  }
} catch (Exception $e) {
  echo json_response(
    code: $e->getCode(),
    message: $e->getMessage(),
  );
}
