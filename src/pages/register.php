<?php
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

$firstname = $lastname = "";
$email = $address = "";
$password = $passwordCheck = "";
$homePhone = $cellPhone = "";
$message = "";
$messageColor = "text-danger";
try {
  setReferer(excludes: ["/login"]);
  if (is_authenticated()) {
    header("Location:" . popReferer());
    exit();
  }
  if (isset($_POST["register"])) {
    $firstname = sanitize_html($_POST["first_name"]);
    $lastname = sanitize_html($_POST["last_name"]);
    $email = sanitize_html($_POST["email"]);
    $address = sanitize_html($_POST["address"]);
    $password = sanitize_html($_POST["password"]);
    $passwordCheck = sanitize_html($_POST["passwordCheck"]);
    $homePhone = sanitize_html($_POST["home_phone"]);
    $cellPhone = sanitize_html($_POST["cell_phone"]);
    $inputs = [
      "first_name" => $firstname,
      "last_name" => $lastname,
      "email" => $email,
      "address" => $cellPhone,
      "password" => $password,
      "home_phone" => $homePhone,
      "cell_phone" => $cellPhone,
    ];
    if (in_array("", $inputs)) {
      $message = "Please fill in all fields";
    }
    if($password != $passwordCheck) {
      $message = "Passwords do not match";
    }
    if (!$message) {
      $message = add_customer($inputs);
    }
    if (!$message) {
      $email = $password = "";
      $firstname = $lastname = "";
      $address = $homePhone = $cellPhone = "";
      $message = "Account Created!";
      $messageColor = "text-success";
      header("Refresh:1;URL=login");
    }
  }
} catch (Exception $e) {
  handle_client_error($e);
}

echo document(
  pageId: "register",
  styles: <<<HTML
  <link href="/src/styles/register.css" rel="stylesheet">
  HTML,
  content: <<<HTML
  <div class="container mb-5">
    <p class="register-page-title">Sign Up</p>
    <hr>
    <form method="post" action="register">
      <div class="row px-2">
        <div class="col-sm-6 px-1">
          <div class="form-floating mb-2">
            <input class="register-form-input form-control" type="text" autocomplete="off" placeholder="First name" name="first_name" value="$firstname" >
            <label>First name</label>
          </div>
        </div>
        <div class="col-sm-6 px-1">
          <div class="form-floating mb-2">
            <input class="register-form-input form-control" type="text" autocomplete="off" placeholder="Last name" name="last_name" value="$lastname">
            <label>Last name</label>
          </div>
        </div>
        <div class="col-sm-6 px-1">
          <div class="form-floating mb-2">
            <input class="register-form-input form-control" type="text" autocomplete="off" name="email" placeholder="Email" value="$email">
            <label>Email</label>
          </div>
        </div>
        <div class="col-sm-6 px-1">
          <div class="form-floating mb-2">
            <input class="register-form-input form-control" type="text" autocomplete="off" placeholder="Home address" name="address" value="$address">
            <label>Home address</label>
          </div>
        </div>
        <div class="col-sm-6 px-1">
          <div class="form-floating mb-2">
            <input class="register-form-input form-control" type="password" autocomplete="off" name="password" placeholder="Password" value="$password">
            <label>Password</label>
          </div>
        </div>
        <div class="col-sm-6 px-1">
          <div class="form-floating mb-2">
            <input class="register-form-input form-control" type="password" autocomplete="off" name="passwordCheck" placeholder="Re-enter Password" value="$password">
            <label>Re-enter Password</label>
          </div>
        </div>
        <div class="col-sm-6 px-1">
          <div class="form-floating mb-2">
            <input class="register-form-input form-control" type="text" autocomplete="off" placeholder="Home phone number" name="home_phone" value="$homePhone">
            <label>Home phone number</label>
          </div>
        </div>
        <div class="col-sm-6 px-1">
          <div class="form-floating mb-2">
            <input class="register-form-input form-control" type="text" autocomplete="off" placeholder="Cell phone number" name="cell_phone" value="$cellPhone">
            <label>Cell phone number</label>
          </div>
        </div>
      </div>
      <div class="text-center mt-4">
        <input class="register-form-btn" type="submit" name="register" value="Submit">
        <p class="register-form-message mt-3 $messageColor">$message</p>
      </div>
    </form>
  </div>
  HTML,
);
