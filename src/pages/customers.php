<?php
require_once("../components/document.php");
require_once("../components/customers.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

/**
 * Filter options
 */
define("OWN_COMPANY", "own");
define("ALL_COMPANIES", "all");

function registerCustomerForm()
{
  $firstname = $lastname = $email = "";
  $address = $homePhone = $cellPhone = "";
  $successMessage = $errorMessage = "";
  if (isset($_POST["register"])) {
    $firstname = sanitizeHTML($_POST["first_name"]);
    $lastname = sanitizeHTML($_POST["last_name"]);
    $email = sanitizeHTML($_POST["email"]);
    $address = sanitizeHTML($_POST["address"]);
    $homePhone = sanitizeHTML($_POST["home_phone"]);
    $cellPhone = sanitizeHTML($_POST["cell_phone"]);
    $inputs = [$firstname, $lastname, $email, $address, $homePhone, $cellPhone];
    for ($i = 0; $i < count($inputs) && $errorMessage == ""; $i++) {
      if ($inputs[$i] == "") {
        $errorMessage = "Please fill in all fields";
      }
    }
    if (!$errorMessage) {
      $errorMessage = addCustomer(...$inputs);
    }
    if (!$errorMessage) {
      $firstname = $lastname = $email = "";
      $address = $homePhone = $cellPhone = "";
      $successMessage = "Customer created";
    }
  }
  $messageText = $errorMessage;
  $messageColor = "text-danger";
  if (!$errorMessage) {
    $messageText = $successMessage;
    $messageColor = "text-success";
  }
  return <<<REGISTER_FORM
  <form method="post" action="customers">
    <div class="row px-2">
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-register-input form-control" type="text" autocomplete="off" placeholder="First name" name="first_name" value="$firstname" >
          <label>First name</label>
        </div>
      </div>
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-register-input form-control" type="text" autocomplete="off" placeholder="Last name" name="last_name" value="$lastname">
          <label>Last name</label>
        </div>
      </div>
    </div>
    <div class="row px-2">
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-register-input form-control" type="text" autocomplete="off" name="email" placeholder="Email address" value="$email">
          <label>Email address</label>
        </div>
      </div>
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-register-input form-control" type="text" autocomplete="off" placeholder="Home address" name="address" value="$address">
          <label>Home address</label>
        </div>
      </div>
    </div>
    <div class="row px-2">
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-register-input form-control" type="text" autocomplete="off" placeholder="Home phone number" name="home_phone" value="$homePhone">
          <label>Home phone number</label>
        </div>
      </div>
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-register-input form-control" type="text" autocomplete="off" placeholder="Cell phone number" name="cell_phone" value="$cellPhone">
          <label>Cell phone number</label>
        </div>
      </div>
    </div>
    <input class="customers-register-btn" type="submit" name="register" value="Submit">
    <span class="customers-register-message ms-2 $messageColor">$messageText</span>
  </form>
  REGISTER_FORM;
}

/**
 * Read customer selected filters
 */
function getSelectedFilters()
{
  $selectedFilters = OWN_COMPANY;
  if (isset($_GET["company"])) {
    $selectedFilters = sanitizeHTML($_GET["company"]);
  }
  return $selectedFilters;
}

/**
 * Create customer select form
 */
function customerSelectForm($selectedFilters = OWN_COMPANY)
{
  $options = "";
  $optionEntries = [
    OWN_COMPANY => "Joe's Java",
    ALL_COMPANIES => "All Companies",
  ];
  foreach ($optionEntries as $value => $text) {
    $selected = $selectedFilters == $value ? "selected" : "";
    $options .= <<<OPTIONS
    <option class="customers-filter-option" value="$value" $selected>
      $text
    </option>
    OPTIONS;
  }
  return <<<SELECT_FORM
  <form id="select-customer-form" class="row" method="get" action="customers">
    <div class="col-lg-6 col-8 pe-1">
      <select class="customers-filter-select form-select" name="company" onchange="submitForm('select-customer-form')">
        $options
      </select>
    </div>
    <div class="col-lg-3 col-4 ps-0">
      <input class="customers-filter-btn" type="submit" value="Apply">
    </div>
  </form>
  SELECT_FORM;
}

/**
 * Read customer search term
 */
function getSearchTerm()
{
  $searchTerm = "";
  if (isset($_GET["search"])) {
    $searchTerm = sanitizeHTML($_GET["search"]);
  }
  return $searchTerm;
}

/**
 * Create customer search form
 */
function customerSearchForm($searchTerm = "")
{
  return <<<SEARCH_FORM
  <form class="row" method="get" action="customers">
    <div class="col-lg-6 col-8 pe-1">
      <input class="customers-search-input form-control" type="text" autocomplete="off" placeholder="🔎︎ Name, email, phone..." name="search" value="$searchTerm" onkeyup="searchCustomers(this)">
    </div>
    <div class="col-lg-3 col-4 ps-0">
      <input class="customers-search-btn" type="submit" value="Find User">
    </div>
    <br>
  </form>
  SEARCH_FORM;
}

function customerList($selectedFilters = OWN_COMPANY, $searchTerm = "")
{
  $customers = [];
  switch ($selectedFilters) {
    case OWN_COMPANY:
      $customers = listCustomers($searchTerm);
      break;
    case ALL_COMPANIES:
      $customers = [];
      break;
    default:
      http_response_code(404);
      include_once("404.php");
      die();
      break;
  }
  return customers($customers);
}

try {
  $registerCustomerForm = registerCustomerForm();
  $selectedFilters = getSelectedFilters();
  $customerSelectForm = customerSelectForm($selectedFilters);
  $searchTerm = getSearchTerm();
  $customerSearchForm = "";
  if ($selectedFilters == OWN_COMPANY) {
    $customerSearchForm = customerSearchForm($searchTerm);
  }
  $customerList = customerList($selectedFilters, $searchTerm);
} catch (Exception $e) {
  http_response_code(400);
  include_once("error.php");
  die();
}

$styles = <<<STYLE
<link href="/src/styles/customers.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div class="container">
  <p class="customers-page-title">Customers</p>
  <hr>
  <p class="customers-form-title mt-2 mb-2">
    Register
  </p>
  $registerCustomerForm
  <p class="customers-form-title mt-5 mb-2">
    Customers
  </p>
  <div class="row">
    <div class="col-md-6 mb-1">
      $customerSelectForm
    </div>
    <div class="col-md-6 mb-3">
      $customerSearchForm
    </div>
  </div>
  <div id="customer-list" class="mb-5">
    $customerList
  </div>
</div>
CONTENT;


$scripts = <<<SCRIPT
<script src="/src/scripts/customers.js" type="text/javascript"></script>
<script src="/src/scripts/utils.js" type="text/javascript"></script>
SCRIPT;

echo document(
  pageId: "customers",
  styles: $styles,
  content: $content,
  scripts: $scripts,
);
