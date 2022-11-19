<?php
function head($pageId = "", $styles = "")
{
  $title = "Joe's Java";
  if (!empty($pageId)) {
    $title .= " |";
    foreach (array_map("ucfirst", explode(" ", $pageId)) as $word) {
      $title .= " $word";
    };
  }
  return <<<HEAD
  <head>
    <title>$title</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../../assets/coffee-beans-icon.png" rel="icon">
    <!-- Style sheets -->
    $styles
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Display&family=Dancing+Script&display=swap" rel="stylesheet">
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
  </head>
  HEAD;
}
