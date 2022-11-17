<?php
require_once("../components/document.php");

try {
  $filePath = "../../assets/contacts.txt";
  $file = fopen($filePath, "r");
  if (!$file) throw new Exception("Failed to open file");
  $entries = explode("\r\n", fread($file, filesize($filePath)));
  $contacts = [];
  foreach ($entries as $entry) {
    if ($entry != "") {
      [$key, $value] = array_map("trim", explode(":", $entry, 2));
      $contacts[$key] = $value;
    }
  }
  $email1 = $contacts["email1"];
  $email2 = $contacts["email2"];
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
  <p class="contacts-page-title">Contact Us</p>
  <hr>
  <div class="contacts-items">
    <div class="contacts-item">
      <p class="contacts-label">Email:</p>
      <ul>
        <li><p class="contacts-value text-muted">$email1</p></li>
        <li><p class="contacts-value text-muted">$email2</p></li>
      </ul>
      <hr>
    </div>
    <div class="contacts-item">
      <p class="contacts-label">Phone Number:</p>
      <ul>
        <li><p class="contacts-value text-muted">$phone</p></li>
      </ul>
      <hr>
    </div>
    <div class="contacts-item">
      <p class="contacts-label">Address:</p>
      <ul>
        <li><p class="contacts-value text-muted">$address</p></li>
      </ul>
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
