<?php
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

/**
 * Product select options
 */
define("ALL_PRODUCTS", "all-products");
define("COFFEE_BEANS", "coffee-beans");
define("BREWING_TOOLS", "brewing-tools");
define("MOST_5_VISITS", "most-5-visits");
define("LAST_5_VISITS", "last-5-visits");

/**
 * Read selected product option
 */
function get_selected_product_option()
{
  $selectedOption = ALL_PRODUCTS;
  if (isset($_GET["category"])) {
    $selectedOption = sanitize_html($_GET["category"]);
  }
  return $selectedOption;
}

/**
 * Create product select form
 */
function product_select_form($selectedOption = ALL_PRODUCTS)
{
  $options = "";
  $optionEntries = [
    ALL_PRODUCTS => "All Products",
    COFFEE_BEANS => "Coffee Beans",
    BREWING_TOOLS => "Brewing Tools",
    MOST_5_VISITS => "Most 5 Visits",
    LAST_5_VISITS => "Last 5 Visits",
  ];
  foreach ($optionEntries as $option => $description) {
    $selected = $selectedOption == $option ? "selected" : "";
    $options .= <<<OPTIONS
    <option class="products-filter-option" value="$option" $selected>
      $description
    </option>
    OPTIONS;
  }
  return <<<SELECT_FORM
  <form id="select-product-form" class="row" method="get" action="products">
    <div class="col-lg-2 col-md-4 col-9 pe-1">
      <select class="products-filter-select form-select" name="category" onchange="submitForm('select-product-form')">
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
 * Create product list based on selected option
 */
function product_list($selectedOption = ALL_PRODUCTS)
{
  $products = [];
  switch ($selectedOption) {
    case ALL_PRODUCTS:
      $products = list_products();
      break;
    case COFFEE_BEANS:
      $products = list_products_by_category(category: "coffee");
      break;
    case BREWING_TOOLS:
      $products = list_products_by_category(category: "brewing-tool");
      break;
    case MOST_5_VISITS:
      $products = list_products_by_most_visited();
      break;
    case LAST_5_VISITS:
      $productIdList = list_visited_product_id();
      $products = list_products_by_id($productIdList);
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
      <p class="products-empty-content">No Product Found</p>
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
  $selectedOption = get_selected_product_option();
  $productSelectForm = product_select_form($selectedOption);
  $productList = product_list($selectedOption);
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
