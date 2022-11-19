<?php
function footer()
{
  $currentYear = date("Y");
  return <<<FOOTER
  <footer id="footer">
    <p class="footer-text">
      © $currentYear. All rights reserved. CMPE-272 Course Project
    </p>
  </footer>
  FOOTER;
}
