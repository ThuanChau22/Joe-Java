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
    $options .= <<<HTML
    <option class="products-filter-option" value="$option" $selected>
      $description
    </option>
    HTML;
  }
  return <<<HTML
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
  HTML;
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
  }
  $productList = "";
  foreach ($products as $product) {
    $productId = $product["id"];
    $productImage = $product["image"];
    $productName = $product["name"];
    $productPrice = $product["price"];
    $productList .= <<<HTML
    <div class="products-card col-xl-2 col-lg-3 col-md-4 col-sm-6">
      <a class="products-card-link" href=/products/$productId>
        <div class="card">
          <img class="card-img-top" src=$productImage>
          <div class="card-body">
            <div class="products-card-name">
              <p class="products-card-name-content">
                $productName
              </p>
            </div>
            <p class="products-card-price">$$productPrice</p>
          </div>
        </div>
      </a>
    </div>
    HTML;
  }
  return count($products) > 0
    ? <<<HTML
  <div class="row">$productList</div>
  HTML
    : <<<HTML
  <div class="products-empty">
    <p class="products-empty-content">No Product Found</p>
  </div>
  HTML;
}

$pageContent = "";
try {
  if (!isset($_GET["id"])) {
    $selectedOption = get_selected_product_option();
    $productSelectForm = product_select_form($selectedOption);
    $productList = product_list($selectedOption);
    $pageContent = <<<HTML
    <p class="products-page-title">Products</p>
    <hr>
    $productSelectForm
    $productList
    HTML;
  } else {
    $productId = sanitize_html($_GET["id"]);
    $product = get_product_by_id($productId);
    $productImage = $product["image"];
    $productName = $product["name"];
    $productPrice = $product["price"];
    $productDescription = $product["description"];
    set_visited_product_id($productId);
    update_product_visited_count($productId);
    $pageContent = <<<HTML
    <div class="pt-5"></div>
    <div class="products-item row">
      <div class="col-md-6">
        <img class="products-item-image img-responsive img-center" src=$productImage>
      </div>
      <div class="col-md-6">
        <p class="products-item-name">$productName</p>
        <div class="pt-1"></div>
        <p class="products-item-price">$$productPrice</p>
        <div class="pt-3"></div>
        <p class="products-item-description-label">Description:</p>
        <p class="products-item-description-content text-muted">$productDescription</p>
      </div>
    </div>
    HTML;
  }
} catch (Exception $e) {
  handle_client_error($e);
}

echo document(
  pageId: "products",
  styles: <<<HTML
  <link href="/src/styles/products.css" rel="stylesheet">
  HTML,
  scripts: <<<HTML
  <script src="/src/scripts/products.js" type="text/javascript"></script>
  <script src="/src/scripts/utils.js" type="text/javascript"></script>
  HTML,
  content: <<<HTML
  <div id="products" class="container">
    $pageContent
    <div class="mb-5"></div>
  </div>
  HTML,
);
