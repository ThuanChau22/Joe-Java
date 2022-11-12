<?php
function footer()
{
  $currentYear = date("Y");
  return <<<FOOTER
  <footer id="footer">
    <p class="copyright">© $currentYear All rights reserved.</p>
    <p class="disclaimer">(CMPE-272 Course Project)</p>
  </footer>
  FOOTER;
}
