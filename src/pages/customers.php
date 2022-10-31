<?php
require_once("../components/document.php");
require_once("../utils/config.php");
require_once("../utils/utils.php");

$firstname = $lastname = $email = "";
$address = $homePhone = $cellPhone = "";
$successMessage = $errorMessage = "";
if (isset($_POST["register"])) {
  try {
    $conn = connectDB();
    $firstname = sanitize($conn, $_POST["first_name"]);
    $lastname = sanitize($conn, $_POST["last_name"]);
    $email = sanitize($conn, $_POST["email"]);
    $address = sanitize($conn, $_POST["address"]);
    $homePhone = sanitize($conn, $_POST["home_phone"]);
    $cellPhone = sanitize($conn, $_POST["cell_phone"]);
    $fields = "first_name, last_name, email, address, home_phone, cell_phone";
    $values = array($firstname, $lastname, $email, $address, $homePhone, $cellPhone);
    foreach ($values as $value) {
      if ($value == "") {
        $errorMessage = "Please fill in all form fields";
        break;
      }
    }
    if (!$errorMessage) {
      try {
        $stmt = $conn->prepare("INSERT INTO customer ($fields) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", ...$values);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        $firstname = $lastname = $email = "";
        $address = $homePhone = $cellPhone = "";
        $successMessage = "Customer created";
      } catch (Exception $e) {
        if ($conn->errno == 1062) {
          $errorMessage = "Customer already existed";
        }
      }
    }
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

$search = "";
$searchResult = "";
$numberOfResults = "";
if (isset($_GET["search"])) {
  try {
    $conn = connectDB();
    $search = sanitize($conn, $_GET["search"]);
    if ($search != "") {
      $types = "";
      $conditions = "";
      $conditionValues = array();
      $conditionFields = array("first_name", "last_name", "email", "home_phone", "cell_phone");
      foreach ($conditionFields as $index => $field) {
        $types .= "s";
        $isLast = $index == count($conditionFields) - 1;
        $conditions .= "$field LIKE ?" . (!$isLast ? " OR " : "");
        array_push($conditionValues, "%$search%");
      }
      $stmt = $conn->prepare("SELECT * FROM customer WHERE $conditions");
      $stmt->bind_param($types, ...$conditionValues);
      $stmt->execute();
      $result = $stmt->get_result();
      $stmt->close();
      $conn->close();
      $entries = "";
      foreach ($result as $row) {
        $entry = "<li><b>Name: </b>" . $row["first_name"] . " " . $row["last_name"] . "</li>";
        $entry .= "<li><b>Email: </b>" . $row["email"] . "</li>";
        $entry .= "<li><b>Address: </b>" . $row["address"] . "</li>";
        $entry .= "<li><b>Home Contact: </b>" . $row["home_phone"] . "</li>";
        $entry .= "<li><b>Mobile Contact: </b>" . $row["cell_phone"] . "</li>";
        $entries .= "<ul>$entry</ul><hr>";
      }
      $numberOfResults = $result->num_rows ." results found!";
      $searchResult = <<<SEARCH_RESULT
      <div class="container">
        $entries
      </div>
      SEARCH_RESULT;
      $result->close();
    }
  } catch (Exception $e) {
    die(header('Location: ./error'));
  }
}

$styles = <<<STYLE
<link href="./src/styles/customers.css" rel="stylesheet">
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
