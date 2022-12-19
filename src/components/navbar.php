<?php
require_once("../utils/database.php");
require_once("../utils/utils.php");

function navbar($pageId)
{
  $pages = ["about", "products", "news", "contacts"];
  $requestURI = $_SERVER["REQUEST_URI"];
  if (isset($_POST["logout"])) {
    remove_session();
    header("Location:$requestURI");
    exit();
  }
  $authItems = <<<HTML
  <li class="nav-item">
     <a class="nav-link" href="/login">Login</a>
  </li>
  <li class="nav-item">
     <a class="nav-link" href="/register">Signup</a>
  </li>
  HTML;
  $number_of_products = 0;
  if (is_authenticated()) {
    $authItems = <<<HTML
    <form class="m-0" method="post" action="$requestURI">
      <input class="auth-link btn btn-link px-0" type="submit" name="logout" value="Logout">
    </form>
    HTML;
    if (is_admin()) {
      $pages[] = "customers";
    }
    $userId = get_session_user()[UID];
    $number_of_products = get_cart_number_of_products($userId);
  } else {
    $number_of_products = get_cart_number_of_products_session();
  }
  if ($number_of_products >= 100) {
    $number_of_products = "99+";
  }
  $navItems = "";
  $style = "style='color:#d9d9d9 !important; font-weight: bold !important'";
  foreach ($pages as $page) {
    $labelName = ucwords(strtolower($page));
    $labelStyle = $page == $pageId ? $style : "";
    $navItems .= <<<HTML
    <li class="nav-item">
      <a class="nav-link" href="/$page" $labelStyle>$labelName</a>
    </li>
    HTML;
  }
  return <<<HTML
  <nav id="navbar" class="navbar navbar-expand-md navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="/home">
        <img class="brand-image" src="../../assets/coffee-beans-icon.png" alt="Joe's Java">
        <span class="brand-text">Joe's Java</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav me-auto">$navItems</ul>
        <ul class="navbar-nav me-2">$authItems</ul>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="/cart">
              <span class="cart-icon material-symbols-outlined">
                shopping_cart
              </span><span id="cart-product-count">$number_of_products</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div id="navbar-padding"></div>
  <script src="/src/scripts/navbar.js" type="text/javascript"></script>
  HTML;
}
