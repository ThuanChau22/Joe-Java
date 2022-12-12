<?php
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

$email = $password = $errorMessage = "";
try {
  setReferer();
  if (valid_session()) {
    header("Location: " . popReferer());
    exit();
  }
  if (isset($_POST["login"])) {
    $email = sanitize_html($_POST["email"]);
    $password = sanitize_html($_POST["password"]);
    if ($email == "" || $password == "") {
      $errorMessage = "Please fill in all form fields.";
    }
    if (!$errorMessage) {
      $errorMessage = login($email, $password);
    }
    if (!$errorMessage) {
      create_session(strtolower($email), isAdmin($email));
      header("Location: " . popReferer());
      exit();
    }
  }
} catch (Exception $e) {
  handle_client_error($e);
}

echo document(
  pageId: "login",
  styles: <<<HTML
  <link href="/src/styles/login.css" rel="stylesheet">
  HTML,
  content: <<<HTML
  <div class="container mb-5">
    <p class="login-page-title">Login</p>
    <hr>
    <form method="post" action="login">
      <div class="row">
        <div class="col-xl-4 col-lg-3 col-md-2"></div>
        <div class="col-xl-4 col-lg-6 col-md-8">
          <div class="form-floating mb-2">
            <input class="login-form-input form-control" type="text" autocomplete="off" placeholder="Email" name="email" value="$email" >
            <label>Email</label>
          </div>
        </div>
        <div class="col-xl-4 col-lg-3 col-md-2"></div>
      </div>
      <div class="row">
        <div class="col-xl-4 col-lg-3 col-md-2"></div>
        <div class="col-xl-4 col-lg-6 col-md-8">
          <div class="form-floating mb-2">
            <input class="login-form-input form-control" type="password" autocomplete="off" placeholder="Password" name="password" value="$password">
            <label>Password</label>
          </div>
        </div>
        <div class="col-xl-4 col-lg-3 col-md-2"></div>
      </div>
      <div class="text-center mt-4">
        <input class="login-form-btn" type="submit" name="login" value="Submit">
        <p class="login-form-message text-danger mt-3">$errorMessage</p>
      </div>
    </form>
  </div>
  HTML,
);
