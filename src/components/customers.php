<?php
require_once("../utils/utils.php");

function customers($customers)
{
  if (count($customers) == 0) {
    return <<<CUSTOMER_EMPTY
    <p class="customers-empty mt-3">No Customer Found</p>
    CUSTOMER_EMPTY;
  }
  $entries = "";
  foreach ($customers as $customer) {
    $firstname = $customer["first_name"];
    $lastname = $customer["last_name"];
    $email = $customer["email"];
    $address = $customer["address"];
    $homePhone = prettyPhoneNumber($customer["home_phone"]);
    $cellPhone = prettyPhoneNumber($customer["cell_phone"]);
    $entries .= <<<CUSTOMER
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
    CUSTOMER;
  }
  return <<<CUSTOMER_LIST
  <div class="container">
    $entries
  </div>
  CUSTOMER_LIST;
}
