<?php
$page_title = 'Case Managers & Solicitors - Vestibular Therapy Associates';
$page_description = 'Information for case managers and solicitors about vestibular rehabilitation services.';
$active_page = 'case-managers';
$extra_scripts = [
  "<script>$(document).ready(function(){ $('.collapsible').collapsible(); });</script>"
];
require_once($_SERVER['DOCUMENT_ROOT'] . '/VTA_NEW/includes/header.php');
?>

<section class="page-header">
  <div class="container">
    <h2>Case Managers &amp; Solicitors</h2>
    <div class="underline"></div>
  </div>
</section>

<section class="section-white" style="padding: 100px 0; text-align: center; min-height: 50vh;">
  <div class="container">
    <i class="material-icons" style="font-size: 64px; color: var(--primary); margin-bottom: 20px;">hourglass_empty</i>
    <h3 style="color: var(--text-dark); font-weight: 700; margin-top: 0;">Coming Soon</h3>
    <p style="color: var(--text-muted); font-size: 18px; max-width: 600px; margin: 20px auto 0;">We are currently updating this page with detailed information for case managers and solicitors. Please check back shortly!</p>
  </div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/VTA_NEW/includes/footer.php'); ?>
