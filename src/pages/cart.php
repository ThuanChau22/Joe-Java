<?php
require_once("../components/cart.php");
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/session.php");
require_once("../utils/utils.php");

function handle_update()
{
  $requestURI = $_SERVER["REQUEST_URI"];
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
      header("Location:$requestURI");
      exit();
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
    if (is_authenticated()) {
      $userId = get_user_session()[UID];
      remove_product_from_cart($userId, $productId);
    } else {
      remove_product_from_cart_session($productId);
    }
    header("Location:$requestURI");
    exit();
  }
}

function handle_checkout()
{
  if (isset($_POST["checkout"])) {
    if (is_authenticated()) {
      $userId = get_user_session()[UID];
      remove_all_products_from_cart($userId);
    } else {
      remove_all_products_from_cart_session();
    }
    $requestURI = $_SERVER["REQUEST_URI"];
    header("Location:$requestURI?checkout_success");
    exit();
  }
}

try {
  handle_update();
  handle_checkout();
  if (isset($_GET["checkout_success"])) {
    $pageContent = <<<HTML
    <div class="cart-checkout-success">
      <p class="text-success">Thank You for Your Purchase!</p>
    </div>
    HTML;
    header("Refresh:3;URL=cart");
  } else {
    if (is_authenticated()) {
      $userId = get_user_session()[UID];
      $cart = list_cart_products($userId);
    } else {
      $cart = list_cart_products_session();
    }
    $pageContent = cart($cart);
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
    <div id="cart-content">
      $pageContent
    </div>
  </div>
  HTML,
);
