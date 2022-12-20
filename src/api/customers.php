<?php
require_once("../components/customers.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

try {
  $searchTerm = "";
  if (isset($_GET["search"])) {
    $searchTerm = sanitize_html($_GET["search"]);
  }
  $customers = list_customers($searchTerm);
  $response = ["data" => $customers];
  if (isset($_GET["html"])) {
    $response = ["html" => customers($customers)];
  }
  echo json_response($response);
} catch (Exception $e) {
  echo json_response(
    code: $e->getCode(),
    message: $e->getMessage(),
  );
}
