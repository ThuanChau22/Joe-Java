<?php
function document($title = "", $content = "", $styles = "", $scripts = "")
{
  $highlightStyle = "style='color:#d9d9d9 !important;'";
  $highlightProducts = $title == "Products" ? $highlightStyle : "";
  $highlightAbout = $title == "About" ? $highlightStyle : "";
  $highlightContacts = $title == "Contacts" ? $highlightStyle : "";
  $highlightNews = $title == "News" ? $highlightStyle : "";
  if (empty($title)) {
    $title = "Joe's Java";
  } else {
    $title = "Joe's Java | $title";
  }
  $currentYear = date("Y");
  return <<<DOCUMENT
  <!DOCTYPE html>
  <html>
    <head>
      <title>$title</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="./src/styles/document.css" rel="stylesheet">
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
    <body>
      <div class="background-image">
        <nav class="navbar navbar-expand-md navbar-dark">
          <div class="container-fluid">
            <a class="navbar-brand" href="./home">Joe's Java</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a class="nav-link" href="./products" $highlightProducts >Products</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="./about" $highlightAbout >About</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="./contacts" $highlightContacts >Contacts</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="./news" $highlightNews >News</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <div id="content" class="container-fluid">
          $content
        </div>
      </div>
      <footer id="footer">
        <p>© Joe's Java $currentYear All rights reserved.</p>
      </footer>
      $scripts
    </body>
  </html>
  DOCUMENT;
}
