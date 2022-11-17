<?php
require_once("../components/document.php");

$styles = <<<STYLE
<link href="./src/styles/home.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div id="home" class="container-fluid">
  <div class="home-cta">
    <p class="home-cta-text-1">Your Perfect</p>
    <p class="home-cta-text-2">Cup of Coffee</p>
    <button class="home-cta-btn" onclick="navigateTo('home-guide-section')">
      Start Here
    </button>
  </div>
  <hr class="home-section-divider">
  <div id="home-guide-section" class="container row mx-auto">
    <div class="col-md-4">
      <img class="home-guide-image img-responsive img-center" src="../../assets/home/coffee-bag.png" alt="">
      <p class="home-guide-content">1. Select beans that suits your taste.</p>
      <a class="home-guide-link" href="#">Explore</a>
      <div class="pb-5"></div>
    </div>
    <div class="col-md-4">
      <img class="home-guide-image img-responsive img-center" src="../../assets/home/coffee-machine.png" alt="">
      <p class="home-guide-content">2. Pick your brewing gear of choice.</p>
      <a class="home-guide-link" href="#">Explore</a>
      <div class="pb-5"></div>
    </div>
    <div class="col-md-4">
      <img class="home-guide-image img-responsive img-center" src="../../assets/home/coffee-beans.png" alt="">
      <p class="home-guide-content">3. Checkout our blog for more brewing guides.</p>
      <a class="home-guide-link" href="./news">Explore</a>
      <div class="pb-5"></div>
    </div>
  </div>
  <hr class="home-section-divider">
  <div class="home-discover-section">
    <a class="home-discover-link" href="./about">Learn More</a>
    <a class="home-discover-link" href="./products">All Products</a>
  </div>
</div>
CONTENT;

$script = <<<SCRIPT
<script src="./src/scripts/utils.js" type="text/javascript"></script>
SCRIPT;

echo document(
  pageId: "home",
  styles: $styles,
  content: $content,
  scripts: $script,
);
