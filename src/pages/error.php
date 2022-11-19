<?php
require_once("../components/document.php");

$styles = <<<STYLE
<link href="/src/styles/error.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div class="error-center">
  <p class="error-title">Oops!</p>
  <p class="error-info">Something went wrong.</p>
  <p class="error-cta">
    <a class="error-link" href="/home">
      <span>Go to our</span>
      <span class="error-link-indicator">home</span>
      <span>page</span>
    </a>
  </p>
</div>
CONTENT;

echo document(
  pageId: "error",
  styles: $styles,
  content: $content,
);
