<?php
require_once("../components/document.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

$isAuthenticated = false;
$username = $errorMessage = "";
if (isset($_POST["username"]) && isset($_POST["password"])) {
  $username = sanitizeHTML($_POST["username"]);
  $password = sanitizeHTML($_POST["password"]);
  if ($username == "" || $password == "") {
    $errorMessage = "Please fill in all form fields.";
  }
  if (!$errorMessage && isset($_POST["signin"])) {
    $errorMessage = login($username, $password);
  }
  if (!$errorMessage) {
    $isAuthenticated = true;
    $username = ucfirst($username);
  }
}

$authForm = <<<AUTH_FORM
<div class="auth-center container">
  <form method="post" action="auth">
    <input type="text" name="username" placeholder="Enter username" autocomplete = "off">
    <br>
    <input type="password" name="password" placeholder="Enter password" autocomplete = "off">
    <br>
    <input type="submit" name="signin" value="Sign In">
    <br>
    $errorMessage
  </form>
</div>
AUTH_FORM;

$authenticated = <<<AUTHENTICATED
<div class="container">
  <p class="auth-greeting">Welcome, $username</p>
</div>
AUTHENTICATED;

$styles = <<<STYLE
<link href="./src/styles/auth.css" rel="stylesheet">
STYLE;

$content = $authForm;
if ($isAuthenticated) {
  $content = $authenticated;
}

echo document(
  pageId: "authentication",
  styles: $styles,
  content: $content,
);
