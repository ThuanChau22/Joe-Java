<?php
require_once("../components/document.php");

$styles = <<<STYLE
<link href="./src/styles/products.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div class="center">
  <h1>Coming Soon!</h1>
</div>
CONTENT;
echo document(title: "Products", content: $content, styles: $styles);
