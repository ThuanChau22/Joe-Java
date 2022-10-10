<?php
require_once("../components/document.php");

$styles = <<<STYLE
<link href="./src/styles/products.css" rel="stylesheet">
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
echo document(pageId: "products", content: $content, styles: $styles);
