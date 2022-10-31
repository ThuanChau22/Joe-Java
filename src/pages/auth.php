<?php
require_once("../components/document.php");
require_once("../utils/config.php");
require_once("../utils/utils.php");

$isAuthenticated = false;
$username = $errorMessage = "";
if (isset($_POST["username"]) && isset($_POST["password"])) {
  try {
    $conn = connectDB();
    $username = sanitize($conn, $_POST["username"]);
    $password = sanitize($conn, $_POST["password"]);
    if ($username == "" || $password == "") {
      $errorMessage = "Please fill in all form fields.";
    }
    if (!$errorMessage && isset($_POST["signin"])) {
      $stmt = $conn->prepare("SELECT password FROM admin WHERE username = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();
      $stmt->close();
      $conn->close();
      $hashPassword = hash("sha512", $password);
      if ($result->num_rows != 0 && $result->fetch_assoc()["password"] == $hashPassword) {
        $isAuthenticated = true;
        $username = ucfirst($username);
      } else {
        $errorMessage = "Incorrect user name or password.";
      }
      $result->close();
    }
  } catch (Exception $e) {
    die(header('Location: ./error'));
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
