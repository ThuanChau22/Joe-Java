<?php
require_once("../components/document.php");
require_once("../components/customers.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

/**
 * Customer select options
 */
define("OWN_COMPANY", "own");
define("ALL_COMPANIES", "all");

/**
 * Create register customer form
 */
function register_customer_form()
{
  $firstname = $lastname = $email = "";
  $address = $homePhone = $cellPhone = "";
  $successMessage = $errorMessage = "";
  if (isset($_POST["register"])) {
    $firstname = sanitize_html($_POST["first_name"]);
    $lastname = sanitize_html($_POST["last_name"]);
    $email = sanitize_html($_POST["email"]);
    $address = sanitize_html($_POST["address"]);
    $homePhone = sanitize_html($_POST["home_phone"]);
    $cellPhone = sanitize_html($_POST["cell_phone"]);
    $inputs = [$firstname, $lastname, $email, $address, $homePhone, $cellPhone];
    for ($i = 0; $i < count($inputs) && $errorMessage == ""; $i++) {
      if ($inputs[$i] == "") {
        $errorMessage = "Please fill in all fields";
      }
    }
    if (!$errorMessage) {
      $errorMessage = add_customer(...$inputs);
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
  return <<<HTML
  <form method="post" action="customers">
    <div class="row px-2">
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-form-input form-control" type="text" autocomplete="off" placeholder="First name" name="first_name" value="$firstname" >
          <label>First name</label>
        </div>
      </div>
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-form-input form-control" type="text" autocomplete="off" placeholder="Last name" name="last_name" value="$lastname">
          <label>Last name</label>
        </div>
      </div>
    </div>
    <div class="row px-2">
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-form-input form-control" type="text" autocomplete="off" name="email" placeholder="Email address" value="$email">
          <label>Email address</label>
        </div>
      </div>
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-form-input form-control" type="text" autocomplete="off" placeholder="Home address" name="address" value="$address">
          <label>Home address</label>
        </div>
      </div>
    </div>
    <div class="row px-2">
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-form-input form-control" type="text" autocomplete="off" placeholder="Home phone number" name="home_phone" value="$homePhone">
          <label>Home phone number</label>
        </div>
      </div>
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-form-input form-control" type="text" autocomplete="off" placeholder="Cell phone number" name="cell_phone" value="$cellPhone">
          <label>Cell phone number</label>
        </div>
      </div>
    </div>
    <input class="customers-form-btn" type="submit" name="register" value="Submit">
    <span class="customers-form-message ms-2 $messageColor">$messageText</span>
  </form>
  HTML;
}

/**
 * Create admin login form
 */
function admin_login_form()
{
  $username = $password = $errorMessage = "";
  if (isset($_POST["login"])) {
    $username = sanitize_html($_POST["username"]);
    $password = sanitize_html($_POST["password"]);
    if ($username == "" || $password == "") {
      $errorMessage = "Please fill in all form fields.";
    }
    if (!$errorMessage) {
      $errorMessage = login($username, $password);
    }
    if (!$errorMessage) {
      create_session(strtolower($username));
      header("Location: " . $_SERVER["REQUEST_URI"]);
    }
  }
  return <<<HTML
  <form method="post" action="customers">
    <div class="row px-2">
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-form-input form-control" type="text" autocomplete="off" placeholder="Username" name="username" value="$username" >
          <label>Username</label>
        </div>
      </div>
      <div class="col-sm-6 px-1">
        <div class="form-floating mb-2">
          <input class="customers-form-input form-control" type="password" autocomplete="off" placeholder="Password" name="password" value="$password">
          <label>Password</label>
        </div>
      </div>
    </div>
    <input class="customers-form-btn" type="submit" name="login" value="Submit">
    <span class="customers-form-message ms-2 text-danger">$errorMessage</span>
  </form>
  HTML;
}

/**
 * Create admin logout form
 */
function logout_form()
{
  return <<<HTML
  <form method="post" action="customers">
    <input class="customers-logout-btn" type="submit" name="logout" value="Logout">
  </form>
  HTML;
}

/**
 * Read selected customer option
 */
function get_selected_customer_option()
{
  $selectedOption = OWN_COMPANY;
  if (isset($_GET["company"])) {
    $selectedOption = sanitize_html($_GET["company"]);
  }
  return $selectedOption;
}

/**
 * Create customer select form
 */
function customer_select_form($selectedOption = OWN_COMPANY)
{
  $options = "";
  $optionEntries = [
    OWN_COMPANY => "Joe's Java",
    ALL_COMPANIES => "All Companies",
  ];
  foreach ($optionEntries as $option => $description) {
    $selected = $selectedOption == $option ? "selected" : "";
    $options .= <<<HTML
    <option class="customers-filter-option" value="$option" $selected>
      $description
    </option>
    HTML;
  }
  return <<<HTML
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
  HTML;
}

/**
 * Read customer search term
 */
function get_search_term()
{
  $searchTerm = "";
  if (isset($_GET["search"])) {
    $searchTerm = sanitize_html($_GET["search"]);
  }
  return $searchTerm;
}

/**
 * Create customer search form
 */
function customer_search_form($searchTerm = "")
{
  return <<<HTML
  <form class="row" method="get" action="customers">
    <div class="col-lg-6 col-8 pe-1 position-relative">
      <input class="customers-search-input form-control pe-4" type="text" autocomplete="off" placeholder="Name, email, phone..." name="search" value="$searchTerm" onkeyup="searchCustomers(this)">
      <div id="search-spinner" class="customers-search-spinner"></div>
    </div>
    <div class="col-lg-3 col-4 ps-0">
      <input class="customers-search-btn" type="submit" value="Find User">
    </div>
    <br>
  </form>
  HTML;
}

/**
 * Create list of customer based on selected option and search term
 */
function customer_list($selectedOption = OWN_COMPANY, $searchTerm = "")
{
  $customers = [];
  switch ($selectedOption) {
    case OWN_COMPANY:
      $customers = list_customers($searchTerm);
      break;
    case ALL_COMPANIES:
      $customers = [];
      break;
  }
  return customers($customers);
}

try {
  if (isset($_POST["logout"])) {
    remove_session();
  }
  $customerFormTitle = "Login";
  $customerForm = admin_login_form();
  $logoutForm = "";
  if (validate_session()) {
    $customerFormTitle = "Register";
    $customerForm = register_customer_form();
    $logoutForm = logout_form();
  }
  $selectedOption = get_selected_customer_option();
  $customerSelectForm = customer_select_form($selectedOption);
  $searchTerm = get_search_term();
  $customerSearchForm = "";
  if ($selectedOption == OWN_COMPANY) {
    $customerSearchForm = customer_search_form($searchTerm);
  }
  $customerList = customer_list($selectedOption, $searchTerm);
} catch (Exception $e) {
  handle_client_error($e);
}

echo document(
  pageId: "customers",
  styles: <<<HTML
  <link href="/src/styles/customers.css" rel="stylesheet">
  HTML,
  scripts: <<<HTML
  <script src="/src/scripts/customers.js" type="text/javascript"></script>
  <script src="/src/scripts/utils.js" type="text/javascript"></script>
  HTML,
  content: <<<HTML
  <div class="container">
    <p class="customers-page-title">Customers</p>
    <hr>
    <div class="row mt-2 mb-2">
      <div class="col-6">
        <p class="customers-form-title">
          $customerFormTitle
        </p>
      </div>
      <div class="col-6">
        $logoutForm
      </div>
    </div>
    $customerForm
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
  HTML,
);
