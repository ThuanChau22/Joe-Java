<?php
function footer()
{
  $currentYear = date("Y");
  return <<<FOOTER
  <footer id="footer">
    <p class="copyright">© Joe's Java $currentYear All rights reserved.</p>
    <p class="disclaimer">(Disclaimer: This is not a real retail website)</p>
  </footer>
  FOOTER;
}
