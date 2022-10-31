<?php
// Log into browser
function consoleLog($data)
{
  if (is_string($data)) {
    $data = "'$data'";
  }
  if (is_array($data) || is_object($data)) {
    $data = "JSON.parse('" . json_encode($data) . "')";
  }
  echo "<script>console.log($data)</script>";
}

// Sanitize user input
function sanitize($conn, $string)
{
  return htmlentities($conn->real_escape_string(trim($string)));
}