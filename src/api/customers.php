<?php
require_once("../components/customers.php");
require_once("../utils/database.php");
require_once("../utils/utils.php");

try {
  $requestMethod = $_SERVER["REQUEST_METHOD"];
  if ($requestMethod == "GET") {
    $searchTerm = "";
    if (isset($_GET["search"])) {
      $searchTerm = sanitize_html($_GET["search"]);
    }
    $customers = list_customers($searchTerm);
    if (isset($_GET["html"])) {
      $customers = customers($customers);
    }
    echo json_response($customers);
  }
} catch (Exception $e) {
  echo json_response(
    code: $e->getCode(),
    message: $e->getMessage(),
  );
}
