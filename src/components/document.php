<?php
require_once("navbar.php");
require_once("footer.php");

function document($title = "", $content = "", $styles = "", $scripts = "")
{
  $navbar = navbar($title);
  if (empty($title)) {
    $title = "Joe's Java";
  } else {
    $title = "Joe's Java | $title";
  }
  $footer = footer();
  return <<<DOCUMENT
  <!DOCTYPE html>
  <html>
    <head>
      <title>$title</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="./src/styles/document.css" rel="stylesheet">
      <link href="./src/styles/navbar.css" rel="stylesheet">
      <link href="./src/styles/footer.css" rel="stylesheet">
      $styles
      <!-- Fonts -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Display&display=swap" rel="stylesheet">
      <!-- Latest compiled and minified CSS -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
      <!-- Latest compiled JavaScript -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body class="background-image">
      $navbar
      <div id="content" class="container-fluid">
        $content
      </div>
      $footer
      $scripts
    </body>
  </html>
  DOCUMENT;
}
