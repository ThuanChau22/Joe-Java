<?php
require_once("head.php");
require_once("navbar.php");
require_once("footer.php");

function document($pageId = "", $content = "", $styles = "", $scripts = "")
{
  $baseStyles = <<<STYLES
  <link href="/src/styles/document.css" rel="stylesheet">
  <link href="/src/styles/navbar.css" rel="stylesheet">
  <link href="/src/styles/footer.css" rel="stylesheet">
  STYLES;
  $head = head($pageId, $baseStyles . $styles);
  $navbar = navbar($pageId);
  $footer = footer();
  return <<<DOCUMENT
  <!DOCTYPE html>
  <html>
    $head
    <body>
      $navbar
      <div class="custom-scroll">
        <div id="content">
          $content
        </div>
        $footer
      </div>
      $scripts
    </body>
  </html>
  DOCUMENT;
}
