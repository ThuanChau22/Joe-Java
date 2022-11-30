<?php
require_once("../utils/database.php");
require_once("../utils/utils.php");

try {
  $searchTerm = "";
  if (isset($_GET["search"])) {
    $searchTerm = sanitize_html($_GET["search"]);
  }
  $customers = list_customers($searchTerm);
  echo json_response($customers);
} catch (Exception $e) {
  echo json_response(
    code: $e->getCode(),
    message: $e->getMessage(),
  );
}
