<?php
require_once("../components/document.php");
require_once("../utils/config.php");

$isAuthenticated = false;
$userName = $password = $successMessage = $errorMessage = "";
if (isset($_POST["userName"]) && isset($_POST["password"])) {
  $userName = sanitize($_POST["userName"]);
  $password = sanitize($_POST["password"]);
  if ($userName == "" || $password == "") {
    $errorMessage = "Please fill in all form fields.";
  }
  if (!$errorMessage && isset($_POST["register"])) {
    if (!array_key_exists($userName, getUsers())) {
      addUser($userName, hash("ripemd128", $password));
      $successMessage = "Register successfully. Please fill in all form fields to sign in.";
    } else {
      $errorMessage = "User name already registered.";
    }
  }
  if (!$errorMessage && isset($_POST["signin"])) {
    $users = getUsers();
    $isUserExisted = array_key_exists($userName, $users);
    $hashPassword = hash("ripemd128", $password);
    if ($isUserExisted && $users[$userName] == $hashPassword) {
      $isAuthenticated = true;
      $userName = ucfirst($userName);
    } else {
      $errorMessage = "Incorrect user name or password.";
    }
  }
}

function getUsers()
{
  try {
    $filePath = "../../assets/password.txt";
    $file = fopen($filePath, "r");
    if (!$file) throw new Exception("Failed to open file");
    $userInfo = array();
    if (filesize($filePath) > 0) {
      $entries = explode("\n", fread($file, filesize($filePath)));
      foreach ($entries as $entry) {
        if ($entry != "") {
          list($key, $value) = explode(" ", $entry, 2);
          $userInfo[$key] = $value;
        }
      }
    }
    return $userInfo;
  } catch (Exception $e) {
    die(header('Location: ./error'));
  } finally {
    fclose($file);
  }
}

function addUser($userName, $password)
{
  try {
    $filePath = "../../assets/password.txt";
    $file = fopen($filePath, "a");
    if (!$file) throw new Exception("Failed to open file");
    fputs($file, "$userName $password\n");
  } catch (Exception $e) {
    die(header('Location: ./error'));
  } finally {
    fclose($file);
  }
}

function sanitize($string)
{
  return htmlentities(preg_replace("/\s+/", "", $string));
}

$authForm = <<<AUTH_FORM
<div class="auth-center container">
  <form class="auth-form" method="post" action="auth">
    <input type="text" name="userName" placeholder="Enter username" autocomplete = "off">
    <br>
    <input type="password" name="password" placeholder="Enter password" autocomplete = "off">
    <br>
    <input type="submit" name="signin" value="Sign In">
    <input type="submit" name="register" value="New User">
    <br>
    $successMessage
    $errorMessage
  </form>
</div>
AUTH_FORM;

$authenticated = <<<AUTHENTICATED
<div class="container">
  <p class="auth-greeting">Welcome, $userName</p>
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
