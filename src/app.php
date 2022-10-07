<?php
require_once("config.php");
require_once("utils.php");

try {
  $conn = new mysqli($_ENV["DB_HOST"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
  // if ($conn->connect_error) throw new Exception("Database failed to connect");
  $value = "013739739";
  $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->bind_param("s", $value);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  if (!$result) throw new Exception("Id not found");
  for ($i = 0; $i < $result->num_rows; $i++) {
    $result->data_seek($i);
    $row = $result->fetch_assoc();
    $name = $row["firstname"] . " " . $row["lastname"];
  }
  $result->close();
  $conn->close();
  $env = $_ENV["ENV"];
  echo <<<_HTML
  <html>
    <head>
      <title>Joe's Java</title>
      <style></style>
    </head>
    <body>
      <p>Name: $name</p>
      <p>Environment: $env</p>
    </body>
  </html>
_HTML;
} catch (Exception $e) {
  die(header('Location: error.php'));
}
