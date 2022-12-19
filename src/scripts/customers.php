<?php
require_once("../components/customers.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

if (isset($_GET["search"])) {
  $searchTerm = sanitize_html($_GET["search"]);
  $customers = list_customers($searchTerm);
  echo json_encode(["html" => customers($customers)]);
}
