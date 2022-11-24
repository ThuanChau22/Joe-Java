<?php
require_once("../components/customers.php");
require_once("../utils/database.php");

if (isset($_GET["search"])) {
  $searchTerm = sanitizeHTML($_GET["search"]);
  $customers = listCustomers($searchTerm);
  echo customers($customers);
}
