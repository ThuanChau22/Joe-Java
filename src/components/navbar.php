<?php
function navbar($pageId)
{
  $navItems = "";
  $pages = ["about", "products", "news", "contacts", "customers"];
  foreach ($pages as $page) {
    $labelName = ucfirst($page);
    if ($page == $pageId) {
      $style = "style='color:#d9d9d9 !important; font-weight: bold !important'";
      $navItems .= <<<NAV_ITEM
      <li class="nav-item">
        <a class="nav-link disabled" $style >$labelName</a>
      </li>
      NAV_ITEM;
    } else {
      $navItems .= <<<NAV_ITEM
      <li class="nav-item">
        <a class="nav-link" href="/$page">$labelName</a>
      </li>
      NAV_ITEM;
    }
  }
  return <<<NAVBAR
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
        <ul class="navbar-nav">$navItems</ul>
      </div>
    </div>
  </nav>
  <div id="navbar-padding"></div>
  <script src="/src/scripts/navbar.js" type="text/javascript"></script>
  NAVBAR;
}
