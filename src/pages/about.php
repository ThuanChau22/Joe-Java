<?php
require_once("../components/document.php");

$content = <<<CONTENT
<h1>About</h1>
CONTENT;
echo document(title: "About", content: $content);