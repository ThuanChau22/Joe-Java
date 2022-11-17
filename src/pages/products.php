<?php
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

$styles = <<<STYLE
<link href="/src/styles/products.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div id="products" class="container">
  <p class="products-page-title">Products</p>
  <hr>
  <div class="products-center">
    <h1>Coming Soon!</h1>
  </div>
</div>
CONTENT;

echo document(
  pageId: "products",
  styles: $styles,
  content: $content,
);

// consoleLog(listProducts());
// consoleLog(listProductsByCategory(category: "coffee"));
// consoleLog(listProductsByCategory(category: "brewing-tool"));
// consoleLog(listProductsByMostVisited());
// $myCookies = ["visitedProductIds" => "[16,2,5,8,14]"];
// $productIds = json_decode($myCookies["visitedProductIds"]);
// consoleLog(listProductsByIds($productIds));
// consoleLog(getProductById(5));

if (isset($_GET["id"])) {
  consoleLog($_GET["id"]);
}
