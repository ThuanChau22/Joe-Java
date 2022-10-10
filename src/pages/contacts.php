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
  <div class="row">
    <div class="col-xl-8 offset-xl-2">
      <p class="contacts-page-greeting lead text-muted">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Non pulvinar neque laoreet suspendisse interdum consectetur libero id faucibus. Dignissim convallis aenean et tortor at risus viverra adipiscing at. Sed risus pretium quam vulputate dignissim suspendisse in. Habitant morbi tristique senectus et netus et malesuada.
      </p>
    </div>
  </div>
</div>
<div class="contacts-items container">
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
CONTENT;

echo document(
  pageId: "contacts",
  styles: $styles,
  content: $content,
);
