<?php
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

$email = $password = $message = "";
$messageColor = "text-danger";
try {
  set_referer(excludes: ["/register"]);
  if (is_authenticated()) {
    header("Location:" . pop_referer());
    exit();
  }
  if (isset($_POST["login"])) {
    $email = sanitize_html($_POST["email"]);
    $password = sanitize_html($_POST["password"]);
    if ($email == "" || $password == "") {
      $message = "Please fill in all form fields.";
    }
    $user = null;
    if (!$message) {
      $result = login($email, $password);
      if(is_string($result)) {
        $message = $result;
      } else {
        $user = $result;
      }
    }
    if (!$message && isset($user)) {
      set_authenticated($user["id"], $user["isAdmin"]);
      $email = $password = "";
      $message = "Login Successful!";
      $messageColor = "text-success";
      header("Refresh:1;URL=" . pop_referer());
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
            <input class="login-form-input form-control" type="text" autocomplete="off" placeholder="Email" name="email" value="$email">
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
        <p class="login-form-message mt-3 $messageColor">$message</p>
      </div>
    </form>
  </div>
  HTML,
);
