<?php
require_once("../components/document.php");

echo document(
  pageId: "error",
  styles: <<<HTML
  <link href="/src/styles/error.css" rel="stylesheet">
  HTML,
  content: <<<HTML
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
  HTML,
);
