<?php
require_once("head.php");
require_once("navbar.php");
require_once("footer.php");

function document($pageId = "", $content = "", $styles = "", $scripts = "")
{
  $baseStyles = "<link href='./src/styles/document.css' rel='stylesheet'>";
  $baseStyles .= "<link href='./src/styles/navbar.css' rel='stylesheet'>";
  $baseStyles .= "<link href='./src/styles/footer.css' rel='stylesheet'>";
  $head = head($pageId, $baseStyles . $styles);
  $navbar = navbar($pageId);
  $footer = footer();
  return <<<DOCUMENT
  <!DOCTYPE html>
  <html>
    $head
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
