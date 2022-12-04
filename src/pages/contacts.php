<?php
require_once("../components/document.php");
require_once("../utils/utils.php");

$email1 = $email2 = "";
$phone = $address = "";
try {
  $filePath = "../../assets/contacts.txt";
  $file = fopen($filePath, "r");
  if (!$file) throw new Exception("Failed to open file", 500);
  $fileContent = fread($file, filesize($filePath));
  $entries = array_map("trim", explode("\n", $fileContent));
  $contacts = [];
  foreach ($entries as $entry) {
    if ($entry != "") {
      [$key, $value] = array_map("trim", explode(":", $entry, 2));
      $contacts[$key] = $value;
    }
  }
  $email1 = $contacts["email1"];
  $email2 = $contacts["email2"];
  $phone = pretty_phone_number($contacts["phone"]);
  $address = $contacts["address"];
} catch (Exception $e) {
  handle_client_error($e);
} finally {
  fclose($file);
}

echo document(
  pageId: "contacts",
  styles: <<<HTML
  <link href="/src/styles/contacts.css" rel="stylesheet">
  HTML,
  content: <<<HTML
  <div class="container">
    <p class="contacts-page-title">Contact Us</p>
    <hr>
    <div class="contacts-items">
      <p class="contacts-label mt-4">Email:</p>
      <ul>
        <li><p class="contacts-value text-muted">$email1</p></li>
        <li><p class="contacts-value text-muted">$email2</p></li>
      </ul>
      <hr>
      <p class="contacts-label mt-4">Phone Number:</p>
      <ul>
        <li><p class="contacts-value text-muted">$phone</p></li>
      </ul>
      <hr>
      <p class="contacts-label mt-4">Address:</p>
      <ul>
        <li><p class="contacts-value text-muted">$address</p></li>
      </ul>
      <hr>
    </div>
  </div>
  HTML,
);
