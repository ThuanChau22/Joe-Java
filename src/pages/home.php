<?php
require_once("../components/document.php");

echo document(
  pageId: "home",
  styles: <<<HTML
  <link href="/src/styles/home.css" rel="stylesheet">
  HTML,
  scripts: <<<HTML
  <script src="/src/scripts/utils.js" type="text/javascript"></script>
  HTML,
  content: <<<HTML
  <div id="home" class="container-fluid">
    <div class="home-cta">
      <p class="home-cta-text-1">Your Perfect</p>
      <p class="home-cta-text-2">Cup of Coffee</p>
      <button class="home-cta-btn" onclick="navigateTo('home-guide-section')">
        Start Here
      </button>
    </div>
    <div id="home-guide-section" class="container row mx-auto">
      <div class="col-md-4">
        <img class="home-guide-image img-responsive img-center" src="../../assets/home/coffee-bag.png" alt="">
        <p class="home-guide-content">1. Select beans that suits your taste.</p>
        <a class="home-guide-link" href="/products?category=coffee-beans">Explore</a>
        <div class="pb-5"></div>
      </div>
      <div class="col-md-4">
        <img class="home-guide-image img-responsive img-center" src="../../assets/home/coffee-machine.png" alt="">
        <p class="home-guide-content">2. Pick your brewing gear of choice.</p>
        <a class="home-guide-link" href="/products?category=brewing-tools">Explore</a>
        <div class="pb-5"></div>
      </div>
      <div class="col-md-4">
        <img class="home-guide-image img-responsive img-center" src="../../assets/home/coffee-beans.png" alt="">
        <p class="home-guide-content">3. Checkout our blog for more brewing guides.</p>
        <a class="home-guide-link" href="/news">Explore</a>
        <div class="pb-5"></div>
      </div>
    </div>
    <div class="home-discover-section">
      <a class="home-discover-link" href="/about">Learn More</a>
      <a class="home-discover-link" href="/products">All Products</a>
    </div>
  </div>
  HTML,
);
