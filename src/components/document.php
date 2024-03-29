<?php
require_once("head.php");
require_once("navbar.php");
require_once("footer.php");

function document($pageId = "", $content = "", $styles = "", $scripts = "")
{
  $styles = <<<HTML
  <link href="/src/styles/document.css" rel="stylesheet">
  <link href="/src/styles/navbar.css" rel="stylesheet">
  <link href="/src/styles/footer.css" rel="stylesheet">
  $styles
  HTML;
  $head = head($pageId, $styles);
  $navbar = navbar($pageId);
  $footer = footer();
  return <<<HTML
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
  HTML;
}
