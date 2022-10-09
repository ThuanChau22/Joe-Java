<?php
function navbar($pageId)
{
  $navItems = "";
  $style = "style='color:#d9d9d9 !important; font-weight: bold !important'";
  $pages = array("products", "about", "contacts", "news");
  foreach ($pages as $page) {
    $labelName = ucfirst($page);
    $labelStyle = $page == $pageId ? $style : "";
    $navItems .= <<<NAV_ITEM
    <li class="nav-item">
      <a class="nav-link" href="./$page" $labelStyle >$labelName</a>
    </li>
    NAV_ITEM;
  }
  return <<<NAVBAR
  <nav class="navbar navbar-expand-md navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="./home">
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
  NAVBAR;
}
