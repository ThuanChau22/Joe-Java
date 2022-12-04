<?php
require_once("../components/document.php");

echo document(
  pageId: "about",
  styles: <<<HTML
  <link href="/src/styles/about.css" rel="stylesheet">
  HTML,
  content: <<<HTML
  <div id="about" class="container-fluid">
    <p class="about-page-title">About Us</p>
    <div class="row">
      <div class="col-xl-2 col-md-0"></div>
      <div class="col-xl-4 col-md-6">
        <img class="about-content-image img-responsive img-center" src="../../assets/about/image-1.jpg" alt="">
      </div>
      <div class="col-xl-4 col-md-6 my-auto">
        <p class="about-content-text">
          We pack your java beans along with our passion. Not just deliver freshly roasted beans, we provide a variety of brewing tools and guides to help you achieve your daily cup of joe that best suits your taste and motivate your body all day long.
        </p>
      </div>
      <div class="col-xl-2 col-md-0"></div>
    </div>
    <div class="row">
      <div class="col-xl-2 col-md-0 order-md-4"></div>
      <div class="col-xl-4 col-md-6 order-md-3">
        <img class="about-content-image img-responsive" src="../../assets/about/image-2.jpg" alt="">
      </div>
      <div class="col-xl-4 col-md-6 order-md-2 my-auto">
        <p class="about-content-text">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Velit ut tortor pretium viverra suspendisse. Nunc sed velit dignissim sodales. Arcu dictum varius duis at consectetur.
        </p>
      </div>
      <div class="col-xl-2 col-md-0 order-md-1"></div>
    </div>
    <div class="row">
      <div class="col-xl-2 col-md-0"></div>
      <div class="col-xl-4 col-md-6">
        <img class="about-content-image img-responsive" src="../../assets/about/image-3.jpg" alt="">
      </div>
      <div class="col-xl-4 col-md-6 my-auto">
        <p class="about-content-text">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Etiam sit amet nisl purus in mollis. Eget sit amet tellus cras adipiscing enim eu turpis. Mauris pharetra et ultrices neque. Facilisis volutpat est velit egestas dui.
        </p>
      </div>
      <div class="col-xl-2 col-md-0"></div>
    </div>
  </div>
  HTML,
);
