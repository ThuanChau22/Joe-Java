<?php
require_once("../components/document.php");

$styles = <<<STYLE
<link href="./src/styles/error.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div class="error-center">
  <p class="error-text">404</p>
  <p class="error-text">Not Found</p>
</div>
CONTENT;

echo document(
  pageId: "error",
  styles: $styles,
  content: $content,
);
