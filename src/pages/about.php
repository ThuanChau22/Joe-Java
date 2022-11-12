<?php
require_once("../components/document.php");

$styles = <<<STYLE
<link href="./src/styles/about.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div id="about" class="container-fluid">
  <p class="about-page-title">About Us</p>
  <div class="row">
    <div class="col-xl-2 col-md-1"></div>
    <div class="col-xl-4 col-md-5">
      <img class="about-content-image img-responsive img-center" src="../../assets/about-coffee-1.jpg" alt="">
    </div>
    <div class="col-xl-4 col-md-5 my-auto">
      <p class="about-content-text">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Euismod quis viverra nibh cras pulvinar mattis nunc sed blandit. Elementum sagittis vitae et leo duis. Dui accumsan sit amet nulla facilisi. Mattis nunc sed blandit libero volutpat.
      </p>
    </div>
    <div class="col-xl-2 col-md-1"></div>
  </div>
  <div class="row">
    <div class="col-xl-2 col-md-1 order-md-4"></div>
    <div class="col-xl-4 col-md-5 order-md-3">
      <img class="about-content-image img-responsive" src="../../assets/about-coffee-2.jpg" alt="">
    </div>
    <div class="col-xl-4 col-md-5 order-md-2 my-auto">
      <p class="about-content-text">
      Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Velit ut tortor pretium viverra suspendisse. Nunc sed velit dignissim sodales. Arcu dictum varius duis at consectetur.
      </p>
    </div>
    <div class="col-xl-2 col-md-1 order-md-1"></div>
  </div>
  <div class="row">
    <div class="col-xl-2 col-md-1"></div>
    <div class="col-xl-4 col-md-5">
      <img class="about-content-image img-responsive" src="../../assets/about-coffee-3.jpg" alt="">
    </div>
    <div class="col-xl-4 col-md-5 my-auto">
      <p class="about-content-text">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Etiam sit amet nisl purus in mollis. Eget sit amet tellus cras adipiscing enim eu turpis. Mauris pharetra et ultrices neque. Facilisis volutpat est velit egestas dui.
      </p>
    </div>
    <div class="col-xl-2 col-md-1"></div>
  </div>
</div>
CONTENT;

echo document(
  pageId: "about",
  styles: $styles,
  content: $content,
);
