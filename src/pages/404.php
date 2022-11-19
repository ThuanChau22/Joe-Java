<?php
require_once("../components/document.php");

$styles = <<<STYLE
<link href="/src/styles/404.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div class="not-found-center">
  <p class="not-found-title">404</p>
  <p class="not-found-info">Page Not Found</p>
  <p class="not-found-cta">
    <a class="not-found-link" href="/home">
      <span>Go to our</span>
      <span class="not-found-link-indicator">home</span>
      <span>page</span>
    </a>
  </p>
</div>
CONTENT;

echo document(
  pageId: "not found",
  styles: $styles,
  content: $content,
);
