<?php
require_once("../components/document.php");

$styles = <<<STYLE
<link href="./src/styles/news.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div class="container">
  <p class="news-page-title">What's News</p>
  <hr>
  <div class="container">
    <div class="news-item container">
      <p class="news-item-date">Oct 02</p>
      <p class="news-item-title">Vulputate eu scelerisque felis imperdiet</p>
      <p class="news-item-content">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Nunc congue nisi vitae suscipit tellus mauris a diam maecenas. Et malesuada fames ac turpis. Lectus mauris ultrices eros in cursus. Neque volutpat ac tincidunt vitae semper quis lectus nulla at...
      </p>
    </div>
    <div class="news-item container">
      <p class="news-item-date">Sep 27</p>
      <p class="news-item-title">Massa placerat duis</p>
      <p class="news-item-content">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Enim nunc faucibus a pellentesque sit. Scelerisque viverra mauris in aliquam sem fringilla ut morbi. Pellentesque nec nam aliquam sem et. Elit ut aliquam purus sit...
      </p>
    </div>
    <div class="news-item container">
      <p class="news-item-date">Sep 15</p>
      <p class="news-item-title">Hac habitasse platea dictumst vestibulum</p>
      <p class="news-item-content">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ultricies tristique nulla aliquet enim tortor. Nibh sit amet commodo nulla facilisi nullam. Risus pretium quam vulputate dignissim suspendisse in est ante. Quis eleifend quam adipiscing vitae proin sagittis nisl rhoncus mattis...
      </p>
    </div>
    <div class="news-item container">
      <p class="news-item-date">Sep 05</p>
      <p class="news-item-title">Blandit massa enim</p>
      <p class="news-item-content">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. At tellus at urna condimentum mattis. Tortor id aliquet lectus proin. Nisi est sit amet facilisis magna etiam tempor. Vel orci porta non pulvinar...
      </p>
    </div>
    <div class="news-item container">
      <p class="news-item-date">Aug 22</p>
      <p class="news-item-title">Risus pretium quam vulputate</p>
      <p class="news-item-content">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Arcu ac tortor dignissim convallis aenean et tortor at. Laoreet id donec ultrices tincidunt arcu non. Nam libero justo laoreet sit amet cursus sit amet. Turpis tincidunt id aliquet risus...
      </p>
    </div>
    <div class="news-item container">
      <p class="news-item-date">Aug 13</p>
      <p class="news-item-title">Leo vel fringilla</p>
      <p class="news-item-content">
      Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis risus sed vulputate odio ut. Gravida dictum fusce ut placerat. Ac turpis egestas maecenas pharetra convallis posuere morbi. At in tellus integer feugiat scelerisque...
      </p>
    </div>
  </div>
</div>
CONTENT;

echo document(
  pageId: "news",
  styles: $styles,
  content: $content,
);
