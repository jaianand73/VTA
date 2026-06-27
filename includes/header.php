<?php
$page_title = $page_title ?? 'Vestibular Therapy Associates';
$page_description = $page_description ?? 'Specialist vestibular rehabilitation for dizziness, vertigo, and balance disorders.';
$active_page = $active_page ?? '';
$extra_head = $extra_head ?? '';
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
  <link rel="icon" href="/VTA_NEW/gallery/favicons/favicon.png" type="image/png">
  <link rel="apple-touch-icon" sizes="180x180" href="/VTA_NEW/gallery/favicons/favicon-180x180.png">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/VTA_NEW/css/material-custom.css">
  <?= $extra_head ?>
</head>
<body>

<div class="navbar-fixed">
  <nav>
    <div class="container nav-wrapper">
      <a href="/VTA_NEW/" class="brand-logo" style="position: relative; display: flex; align-items: center; height: 100%;">
        <img src="/VTA_NEW/gallery/Logo.png" alt="VTA" style="width: auto; display: block;">
      </a>
      <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
      <ul class="right hide-on-med-and-down">
        <li><a href="/VTA_NEW/"<?= $active_page === 'home' ? ' class="active"' : '' ?>>Home</a></li>
        <li><a href="/VTA_NEW/About-Us/"<?= $active_page === 'about' ? ' class="active"' : '' ?>>About Us</a></li>
        <li><a href="/VTA_NEW/How-We-Help/"<?= $active_page === 'how-we-help' ? ' class="active"' : '' ?>>How We Help</a></li>
        <li><a href="/VTA_NEW/Meet-the-Team/"<?= $active_page === 'team' ? ' class="active"' : '' ?>>Meet the Team</a></li>
        <li><a href="/VTA_NEW/Contact-Us/"<?= $active_page === 'contact' ? ' class="active"' : '' ?>>Contact Us</a></li>
        <li><a href="/VTA_NEW/Case-Managers-Solicitors/"<?= $active_page === 'case-managers' ? ' class="active"' : '' ?>>Case Managers &amp; Solicitors</a></li>
        <li><a href="/vta-portal/switch-user"<?= $active_page === 'vta-login' ? ' class="active"' : '' ?>><i class="fas fa-lock" style="margin-right:4px; font-size:12px;"></i>Login to VTA</a></li>
      </ul>
    </div>
  </nav>
</div>
<ul class="sidenav" id="mobile-nav">
  <li class="<?= $active_page === 'home' ? 'active' : '' ?>"><a href="/VTA_NEW/">Home</a></li>
  <li class="<?= $active_page === 'about' ? 'active' : '' ?>"><a href="/VTA_NEW/About-Us/">About Us</a></li>
  <li class="<?= $active_page === 'how-we-help' ? 'active' : '' ?>"><a href="/VTA_NEW/How-We-Help/">How We Help</a></li>
  <li class="<?= $active_page === 'team' ? 'active' : '' ?>"><a href="/VTA_NEW/Meet-the-Team/">Meet the Team</a></li>
  <li class="<?= $active_page === 'contact' ? 'active' : '' ?>"><a href="/VTA_NEW/Contact-Us/">Contact Us</a></li>
  <li class="<?= $active_page === 'case-managers' ? 'active' : '' ?>"><a href="/VTA_NEW/Case-Managers-Solicitors/">Case Managers &amp; Solicitors</a></li>
  <li><a href="/vta-portal/switch-user"><i class="fas fa-lock" style="margin-right:4px; font-size:12px;"></i> Login to VTA</a></li>
</ul>
