<?php
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

$productId = "";
$productId = "";
if (isset($_GET["id"])) {
  $productId = $_GET["id"];
}
$product = getProductById($productId);
$productImage = $product["image"];
$productName = $product["name"];
$productDescription = $product["description"];
addCookieProduct($productId);
increaseProductVisitedCount($productId);

$styles = <<<STYLE
<link href="/src/styles/item.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div class="container">
  <div class="pt-5"></div>
  <div class="row">
    <div class="col-md-6">
      <img class="item-image img-responsive img-center" src="$productImage">
    </div>
    <div class="col-md-6">
      <p class="item-name">$productName</p>
      <div class="pt-4"></div>
      <p class="item-description-label">Description:</p>
      <p class="item-description-content text-muted">$productDescription</p>
    </div>
  </div>
  <div class="pt-5"></div>
</div>
CONTENT;

echo document(
  pageId: "products",
  styles: $styles,
  content: $content,
);
