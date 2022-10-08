<?php
require_once("../components/document.php");

$styles = <<<STYLE
<link href="./src/styles/error.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div class="center">
  <h1>404</h1>
  <h1>Not Found</h1>
</div>
CONTENT;

echo document(title: "Error", content: $content, styles: $styles);
