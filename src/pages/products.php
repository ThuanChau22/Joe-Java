<?php
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

/**
 * Filter options
 */
define("ALL_PRODUCTS", "all-products");
define("COFFEE_BEANS", "coffee-beans");
define("BREWING_TOOLS", "brewing-tools");
define("MOST_5_VISITS", "most-5-visits");
define("LAST_5_VISITS", "last-5-visits");

/**
 * Read selected filters
 */
function getSelectedFilters()
{
  $selectedFilters = ALL_PRODUCTS;
  if (isset($_GET["filters"])) {
    $selectedFilters = sanitizeHTML($_GET["filters"]);
  }
  return $selectedFilters;
}

/**
 * Create products select form
 */
function productSelectForm($selectedFilters = ALL_PRODUCTS)
{
  $options = "";
  $optionEntries = [
    ALL_PRODUCTS => "All Products",
    COFFEE_BEANS => "Coffee Beans",
    BREWING_TOOLS => "Brewing Tools",
    MOST_5_VISITS => "Most 5 Visits",
    LAST_5_VISITS => "Last 5 Visits",
  ];
  foreach ($optionEntries as $value => $text) {
    $selected = $selectedFilters == $value ? "selected" : "";
    $options .= <<<OPTIONS
    <option class="products-filter-option" value="$value" $selected>
      $text
    </option>
    OPTIONS;
  }
  return <<<SELECT_FORM
  <form id="select-product-form" class="row" method="get" action="products">
    <div class="col-lg-2 col-md-4 col-9 pe-1">
      <select class="products-filter-select form-select" name="filters" onchange="submitForm('select-product-form')">
        $options
      </select>
    </div>
    <div class="col-lg-1 col-md-2 col-3 ps-0">
      <input class="products-filter-btn" type="submit" value="Apply">
    </div>
  </form>
  SELECT_FORM;
}

/**
 * List products based on selected filters
 */
function productList($selectedFilters = ALL_PRODUCTS)
{
  $products = [];
  switch ($selectedFilters) {
    case ALL_PRODUCTS:
      $products = listProducts();
      break;
    case COFFEE_BEANS:
      $products = listProductsByCategory(category: "coffee");
      break;
    case BREWING_TOOLS:
      $products = listProductsByCategory(category: "brewing-tool");
      break;
    case MOST_5_VISITS:
      $products = listProductsByMostVisited();
      break;
    case LAST_5_VISITS:
      $productIds = getCookieProducts();
      $products = listProductsByIds($productIds);
      break;
    default:
      http_response_code(404);
      include_once("404.php");
      die();
      break;
  }
  if (count($products) == 0) {
    return <<<PRODUCT_EMPTY
    <div class="products-empty">
      <p class="products-empty-content">Empty</p>
    </div>
    PRODUCT_EMPTY;
  }
  $productList = "";
  foreach ($products as $product) {
    $productId = $product["id"];
    $productImage = $product["image"];
    $productName = $product["name"];
    $productList .= <<<PRODUCT_CARD
    <div class="products-card col-xl-2 col-lg-3 col-md-4 col-sm-6">
      <a class="products-card-link" href=/products/$productId>
        <div class="card">
          <img class="card-img-top" src="$productImage">
          <div class="card-body">
            <div class="products-card-name">
              <p class="products-card-name-content">
                $productName
              </p>
            </div>
          </div>
        </div>
      </a>
    </div>
    PRODUCT_CARD;
  }
  return <<<PRODUCT_LIST
  <div class="row">
    $productList
  </div>
  PRODUCT_LIST;
}

try {
  $selectedFilters = getSelectedFilters();
  $productSelectForm = productSelectForm($selectedFilters);
  $productList = productList($selectedFilters);
} catch (Exception $e) {
  http_response_code(400);
  include_once("error.php");
  die();
}

$styles = <<<STYLE
<link href="/src/styles/products.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div id="products" class="container">
  <p class="products-page-title">Products</p>
  <hr>
  $productSelectForm
  $productList
  <div class="pt-5"></div>
</div>
CONTENT;

$scripts = <<<SCRIPT
<script src="/src/scripts/products.js" type="text/javascript"></script>
<script src="/src/scripts/utils.js" type="text/javascript"></script>
SCRIPT;

echo document(
  pageId: "products",
  styles: $styles,
  content: $content,
  scripts: $scripts,
);
