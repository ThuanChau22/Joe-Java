<?php
require_once("../components/document.php");

try {
  $filePath = "../../assets/contacts.txt";
  $file = fopen($filePath, "r");
  if (!$file) throw new Exception("Failed to open file");
  $entries = explode("\n", fread($file, filesize($filePath)));
  $contacts = array();
  foreach ($entries as $entry) {
    list($key, $value) = explode(":", $entry, 2);
    $contacts[trim($key)] = trim($value);
  }
  $email = $contacts["email"];
  $phone = $contacts["phone"];
  $address = $contacts["address"];
} catch (Exception $e) {
  die(header('Location: ./error'));
} finally {
  fclose($file);
}

$styles = <<<STYLE
<link href="./src/styles/contacts.css" rel="stylesheet">
STYLE;

$content = <<<CONTENT
<div class="container">
  <p class="contacts-page-title">Contacts Us</p>
  <hr>
  <div class="contacts-items container">
    <div class="contacts-item">
      <p class="contacts-label">Email:</p>
      <p class="contacts-value">• $email</p>
      <hr>
    </div>
    <div class="contacts-item">
      <p class="contacts-label">Phone Number:</p>
      <p class="contacts-value">• $phone</p>
      <hr>
    </div>
    <div class="contacts-item">
      <p class="contacts-label">Address:</p>
      <p class="contacts-value">• $address</p>
      <hr>
    </div>
  </div>
</div>
CONTENT;

echo document(
  pageId: "contacts",
  styles: $styles,
  content: $content,
);
