<?php
require_once("../components/document.php");

$content = <<<CONTENT
<h1>News</h1>
CONTENT;
echo document(pageId: "news", content: $content);
