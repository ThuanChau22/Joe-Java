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
<div>
  <h1>Contacts Us</h1>
  <p><span>Email:</span><span>$email</span></p>
  <p><span>Phone Number:</span><span>$phone</span></p>
  <p><span>Address:</span><span>$address</span></p>
</div>
CONTENT;
echo document(title: "Contacts", content: $content);
