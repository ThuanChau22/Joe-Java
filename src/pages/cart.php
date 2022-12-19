<?php
require_once("../components/cart.php");
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

function update_cart_product($productId, $quantity)
{
  if (is_authenticated()) {
    $userId = get_session_user()[UID];
    set_product_to_cart($userId, $productId, $quantity);
  } else {
    set_product_to_cart_session($productId, $quantity);
  }
}

function delete_cart_product($productId)
{
  if (is_authenticated()) {
    $userId = get_session_user()[UID];
    remove_product_from_cart($userId, $productId);
  } else {
    remove_product_from_cart_session($productId);
  }
}

function handle_update()
{
  $isUpdate = isset($_POST["update_to_cart"]);
  $isDelete = isset($_POST["delete_from_cart"]);
  if (($isUpdate || $isDelete) && isset($_POST["product_id"])) {
    $productId = sanitize_html($_POST["product_id"]);
    if ($isDelete) {
      delete_cart_product($productId);
    }
    if (
      $isUpdate
      && isset($_POST["old_quantity"])
      && isset($_POST["new_quantity"])
    ) {
      $oldQuantity = sanitize_html($_POST["old_quantity"]);
      $newQuantity = sanitize_html($_POST["new_quantity"]);
      $isValid = $newQuantity != "" && is_numeric($newQuantity);
      $isValid = $isValid && $oldQuantity != intval($newQuantity);
      if ($isValid && $newQuantity == 0) {
        delete_cart_product($productId);
      }
      if ($isValid && $newQuantity > 0) {
        $quantity = intval($newQuantity) - $oldQuantity;
        update_cart_product($productId, $quantity);
      }
    }
    header("Location:/cart");
    exit();
  }
}

function handle_checkout()
{
  if (isset($_POST["checkout"])) {
    if (is_authenticated()) {
      $userId = get_session_user()[UID];
      remove_all_products_from_cart($userId);
    } else {
      remove_all_products_from_cart_session();
    }
    header("Location:/cart?checkout_success");
    exit();
  }
}

function checkout_success()
{
  header("Refresh:3;URL=/cart");
  return <<<HTML
  <div class="cart-checkout-success">
    <p class="text-success">Thank You for Your Purchase!</p>
  </div>
  HTML;
}

function fetch_cart()
{
  if (is_authenticated()) {
    $userId = get_session_user()[UID];
    $cart = list_cart_products($userId);
  } else {
    $cartSession = list_cart_products_session();
    $productIds = $cartSession[PRODUCT_IDS];
    $quantities = $cartSession[QUANTITIES];
    $cart = list_products_by_id($productIds);
    foreach ($cart as $i => $product) {
      $cart[$i]["quantity"] = $quantities[$product["id"]];
    }
  }
  return $cart;
}

try {
  handle_update();
  handle_checkout();
  if (isset($_GET["checkout_success"])) {
    $pageContent = checkout_success();
  } else {
    $pageContent = cart(fetch_cart());
  }
} catch (Exception $e) {
  handle_client_error($e);
}

echo document(
  pageId: "cart",
  styles: <<<HTML
  <link href="/src/styles/cart.css" rel="stylesheet">
  HTML,
  scripts: <<<HTML
  <script src="/src/scripts/cart.js" type="text/javascript"></script>
  <script src="/src/scripts/utils.js" type="text/javascript"></script>
  HTML,
  content: <<<HTML
  <div class="container mb-5">
    <p class="cart-page-title">Your Cart</p>
    <hr>
    $pageContent
  </div>
  HTML,
);
