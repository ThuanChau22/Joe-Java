<?php
require_once("../utils/database.php");
require_once("../utils/utils.php");

if (isset($_POST["add_to_cart"]) && isset($_POST["product_id"])) {
  $number_of_products = 0;
  $productId = sanitize_html($_POST["product_id"]);
  if (is_authenticated()) {
    $userId = get_session_user()[UID];
    set_product_to_cart($userId, $productId);
    $number_of_products = get_cart_number_of_products($userId);
  } else {
    set_product_to_cart_session($productId);
    $cart = list_cart_products_session();
    foreach ($cart[QUANTITIES] as $quantity) {
      $number_of_products += $quantity;
    }
  }
  if ($number_of_products >= 100) {
    $number_of_products = "99+";
  }
  echo $number_of_products;
}
