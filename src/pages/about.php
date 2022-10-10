<?php
require_once("../components/document.php");

$styles = <<<STYLE
<link href="./src/styles/about.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div id="about" class="container-fluid">
  <p class="about-page-title">About Us</p>
  <div class="row">
    <div class="col-xl-6">
      <img class="about-content-image img-responsive img-center" src="../../assets/about-coffee-1.jpg" alt="">
    </div>
    <div class="col-xl-6 my-auto">
      <p class="about-content-text">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Euismod quis viverra nibh cras pulvinar mattis nunc sed blandit. Elementum sagittis vitae et leo duis. Dui accumsan sit amet nulla facilisi. Mattis nunc sed blandit libero volutpat. Porttitor rhoncus dolor purus non enim praesent elementum facilisis leo. Eleifend donec pretium vulputate sapien nec. Cursus risus at ultrices mi tempus imperdiet nulla. Tellus integer feugiat scelerisque varius. Semper risus in hendrerit gravida rutrum quisque non tellus.
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-6 order-xl-2">
      <img class="about-content-image img-responsive" src="../../assets/about-coffee-2.jpg" alt="">
    </div>
    <div class="col-xl-6 order-xl-1 my-auto">
      <p class="about-content-text">
      Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Velit ut tortor pretium viverra suspendisse. Nunc sed velit dignissim sodales. Arcu dictum varius duis at consectetur. Dui faucibus in ornare quam. Malesuada pellentesque elit eget gravida. Facilisis gravida neque convallis a cras. Augue ut lectus arcu bibendum at varius vel.
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-6">
      <img class="about-content-image img-responsive" src="../../assets/about-coffee-3.jpg" alt="">
    </div>
    <div class="col-xl-6 my-auto">
      <p class="about-content-text">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Etiam sit amet nisl purus in mollis. Eget sit amet tellus cras adipiscing enim eu turpis. Mauris pharetra et ultrices neque. Facilisis volutpat est velit egestas dui. Viverra suspendisse potenti nullam ac tortor vitae purus faucibus ornare. Lobortis elementum nibh tellus molestie nunc non blandit massa enim. At tellus at urna condimentum mattis pellentesque id nibh. Ut porttitor leo a diam sollicitudin tempor id eu. Nisi porta lorem mollis aliquam ut porttitor leo. Dis parturient montes nascetur ridiculus mus mauris vitae. Amet cursus sit amet dictum sit amet justo.
      </p>
    </div>
  </div>
</div>
CONTENT;

echo document(
  pageId: "about",
  styles: $styles,
  content: $content,
);
