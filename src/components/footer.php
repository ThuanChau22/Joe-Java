<?php
function footer()
{
  $currentYear = date("Y");
  return <<<FOOTER
  <footer id="footer">
    <p class="copyright">© $currentYear All rights reserved.</p>
    <p class="disclaimer">(Disclaimer: This is not a real retail website)</p>
  </footer>
  FOOTER;
}
