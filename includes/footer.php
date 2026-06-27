<?php
$extra_scripts = $extra_scripts ?? [];
?>
<footer class="page-footer">
  <div class="container">
    <div class="row" style="margin-bottom:0;">
      <div class="col s12 m4">
        <div class="footer-logo">
          <a href="/VTA_NEW/"><img src="/VTA_NEW/gallery_gen/5d833ffaf07f40f7045255307e7b657a_414x414_fit.png" alt="VTA"></a>
        </div>
      </div>
      <div class="col s12 m4 offset-m4">
        <div class="footer-address">
          <p><strong>DizzyCare Clinic</strong></p>
          <p>Oaktree House, Oaktree Rise,</p>
          <p>Codsall, Wolverhampton</p>
          <p>WV8 1DP</p>
          <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="X (Twitter)"><i class="fab fa-x-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-copyright">
    <div class="container center">
      &copy; 2026 Vestibular Therapy Associates. All rights reserved.
    </div>
  </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>$(document).ready(function(){ $('.sidenav').sidenav(); });</script>
<?php foreach ($extra_scripts as $script): ?>
<?= $script ?>
<?php endforeach; ?>
</body>
</html>
