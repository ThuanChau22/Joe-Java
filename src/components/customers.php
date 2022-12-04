<?php
require_once("../utils/utils.php");

function customers($customers)
{
  $customerList = "";
  foreach ($customers as $customer) {
    $firstname = $customer["first_name"];
    $lastname = $customer["last_name"];
    $email = $customer["email"];
    $address = $customer["address"];
    $homePhone = pretty_phone_number($customer["home_phone"]);
    $cellPhone = pretty_phone_number($customer["cell_phone"]);
    $customerList .= <<<HTML
    <div class="customers-entry mb-4">
      <p class="customers-entry-name">$firstname $lastname</p>
      <hr>
      <p class="customers-entry-label mt-2">Email:</p>
      <ul>
        <li><p class="customers-entry-value text-muted">$email</p></li>
      </ul>
      <hr>
      <p class="customers-entry-label mt-2">Phone Number:</p>
      <ul>
        <li><p class="customers-entry-value text-muted">Home: $homePhone</p></li>
        <li><p class="customers-entry-value text-muted">Mobile: $cellPhone</p></li>
      </ul>
      <hr>
      <p class="customers-entry-label mt-2">Address:</p>
      <ul>
        <li><p class="customers-entry-value text-muted">$address</p></li>
      </ul>
      <hr>
    </div>
    HTML;
  }
  return count($customers) > 0
    ? <<<HTML
    <div class="container">$customerList</div>
    HTML
    : <<<HTML
    <p class="customers-empty mt-3">No Customer Found</p>
    HTML;
}
