<?php
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

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
      $errorMessage = "Please fill in all form fields";
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

$search = "";
$searchResult = "";
$numberOfResults = "";
if (isset($_GET["search"])) {
  try {
    $conn = connectDB();
    $search = sanitizeHTML($_GET["search"]);
    if ($search != "") {
      $customers = listCustomers($search);
      $entries = "";
      foreach ($customers as $customer) {
        $name = $customer["first_name"] . " " . $customer["last_name"];
        $entry = "<li><b>Name: </b>" . $name . "</li>";
        $entry .= "<li><b>Email: </b>" . $customer["email"] . "</li>";
        $entry .= "<li><b>Address: </b>" . $customer["address"] . "</li>";
        $entry .= "<li><b>Home Contact: </b>" . $customer["home_phone"] . "</li>";
        $entry .= "<li><b>Mobile Contact: </b>" . $customer["cell_phone"] . "</li>";
        $entries .= "<ul>$entry</ul><hr>";
      }
      $numberOfResults = count($customers) . " results found!";
      $searchResult = <<<SEARCH_RESULT
      <div class="container">
        $entries
      </div>
      SEARCH_RESULT;
    }
  } catch (Exception $e) {
    include_once("error.php");
    die();
  }
}

$styles = <<<STYLE
<link href="/src/styles/customers.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div class="container">
  <p class="customers-page-title">Customers</p>
  <hr>
  <form method="post" action="customers">
    <input type="text" name="first_name" value="$firstname" placeholder="First name" autocomplete="off">
    <br>
    <input type="text" name="last_name" value="$lastname" placeholder="Last name" autocomplete="off">
    <br>
    <input type="text" name="email" value="$email" placeholder="Email address" autocomplete="off">
    <br>
    <input type="text" name="address" value="$address" placeholder="Home address" autocomplete="off">
    <br>
    <input type="text" name="home_phone" value="$homePhone" placeholder="Home phone number" autocomplete="off">
    <br>
    <input type="text" name="cell_phone" value="$cellPhone" placeholder="Cell phone number" autocomplete="off">
    <br>
    <input type="submit" name="register" value="Create User">
    <br>
    $successMessage
    $errorMessage
  </form>
  <hr>
  <form method="get" action="customers">
    <input type="text" name="search" value="$search" placeholder="Names, email, phone numbers..." autocomplete="off">
    <input type="submit" value="Search User">
    $numberOfResults
    <br>
  </form>
  $searchResult
</div>
CONTENT;

echo document(
  pageId: "customers",
  styles: $styles,
  content: $content,
);
