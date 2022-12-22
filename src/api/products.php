<?php
require_once("../utils/database.php");
require_once("../utils/utils.php");

try {
  $requestMethod = $_SERVER["REQUEST_METHOD"];
  if ($requestMethod == "GET") {
    if (isset($_GET["id"])) {
      $id = sanitize_html($_GET["id"]);
      $product = get_product_by_id($id);
      echo json_response($product);
    } else {
      $category = "";
      if (isset($_GET["category"])) {
        $category = sanitize_html($_GET["category"]);
      }
      $limit = 5;
      if (isset($_GET["limit"])) {
        $limit = sanitize_html($_GET["limit"]);
      }
      $products = [];
      switch ($category) {
        case 'coffee-beans':
          $products = list_products_by_category(category: "coffee");
          break;
        case 'brewing-tools':
          $products = list_products_by_category(category: "brewing-tool");
          break;
        case 'most-visits':
          $products = list_products_by_most_visited($limit);
          break;
        default:
          $products = list_products();
          break;
      }
      echo json_response($products);
    }
  }
} catch (Exception $e) {
  echo json_response(
    code: $e->getCode(),
    message: $e->getMessage(),
  );
}
