<?php
require_once("../utils/utils.php");

function getCustomers()
{
  $url = "http://localhost:8000/api/customers";
  $curlHandler = curl_init($url);
  curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($curlHandler);
  $httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
  $content = json_decode($response, true);
  console_log($httpCode);
  console_log($content);
  curl_close($curlHandler);
}

function addCustomer()
{
  $url = "http://localhost:8000/customers";
  $curlHandler = curl_init($url);
  // $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
  // curl_setopt($curlHandler, CURLOPT_COOKIE, $strCookie);
  // curl_setopt($curlHandler, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
  // curl_setopt($curlHandler, CURLOPT_PORT, true);
  // $postFields = "register=Submit&first_name=test&last_name=test&email=test@test.com&address=123somewhere&home_phone=4123123123&cell_phone=7456456456";
  // curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $postFields);

  $payload = json_encode([
    "username" => "tc22",
    "password" => "Tt123123",
  ]);

  curl_setopt($curlHandler, CURLINFO_HEADER_OUT, true);
  curl_setopt($curlHandler, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Content-Length:" . strlen($payload),
  ]);
  curl_setopt($curlHandler, CURLOPT_PORT, true);
  curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $payload);

  curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($curlHandler);
  console_log($response);
  curl_close($curlHandler);
}

function printCustomers()
{
  $url = "http://localhost:8000/customers";
  $curlHandler = curl_init($url);
  curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($curlHandler);
  echo $response;
  curl_close($curlHandler);
}

echo "Check browser console";
// getCustomers();
// addCustomer();
printCustomers();
