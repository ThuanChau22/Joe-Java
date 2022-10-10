<?php
require_once("../components/document.php");
// try {
//   $conn = connectDB();
//   $value = "013739739";
//   $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
//   $stmt->bind_param("s", $value);
//   $stmt->execute();
//   $result = $stmt->get_result();
//   $stmt->close();
//   $conn->close();
//   if (!$result) throw new Exception("Request failed");
//   for ($i = 0; $i < $result->num_rows; $i++) {
//     $result->data_seek($i);
//     $row = $result->fetch_assoc();
//     $name = $row["firstname"] . " " . $row["lastname"];
//   }
//   $result->close();
//   $env = $_ENV["ENV"];
//   $body = <<<BODY
//     <p>Name: $name</p>
//     <p>Environment: $env</p>
//   BODY;
//   echo Document($body, "Home");
// } catch (Exception $e) {
//   die(header('Location: ./error'));
// }

$content = <<<CONTENT
<h1>Home</h1>
CONTENT;
echo document(pageId: "home", content: $content);
