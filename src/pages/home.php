<?php
require_once("../components/document.php");

$content = <<<CONTENT
<h1>Home</h1>
CONTENT;
echo document(pageId: "home", content: $content);
