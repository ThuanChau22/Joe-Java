<?php
require_once("config.php");
require_once("utils.php");

$conn = new mysqli($_ENV["DB_HOST"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if ($conn->connect_error) {
  echo $conn->connect_error;
}

$value = "tc";
$stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
$stmt->bind_param("s", $value);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
if (!$result) {
  echo $result;
}

for ($i = 0; $i < $result->num_rows; $i++) {
  $result->data_seek($i);
  $row = $result->fetch_assoc();
  $name = $row["name"];
  $age = $row["age"];
}
$port = $_SERVER["SERVER_PORT"];
$env = $_ENV["ENV"];

$result->close();
$conn->close();

echo <<<_HTML
<html>
  <head>
    <title>Joe's Java</title>
    <style></style>
  </head>
  <body>
    <p>Name: $name</p>
    <p>Age: $age</p>
    <p>Running on port: $port</p>
    <p>Environment: $env</p>
  </body>
</html>
_HTML;
