<?php
function navbar($title)
{
  $style = "style='color:#d9d9d9 !important; font-weight: bold !important'";
  $productsStyle = $title == "Products" ? $style : "";
  $aboutStyle = $title == "About" ? $style : "";
  $contactsStyle = $title == "Contacts" ? $style : "";
  $newsStyle = $title == "News" ? $style : "";
  return <<<NAVBAR
  <nav class="navbar navbar-expand-md navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="./home">Joe's Java</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="./products" $productsStyle >Products</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./about" $aboutStyle >About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./contacts" $contactsStyle >Contacts</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./news" $newsStyle >News</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  NAVBAR;
}
