<?php require_once __DIR__ . '/includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Dashboard') ?> | ID Card Generator</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="layout">
<aside class="sidebar" id="sidebar">
  <a class="brand" href="index.php">
    <img src="assets/images/logo.jpg" alt="Logo" class="brand-logo">
    <span>ID Card<b>Generator</b></span>
  </a>
  <nav class="side-nav" style="margin-top:8px">
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">All ID Cards</a>
    <a href="add.php" class="<?= basename($_SERVER['PHP_SELF']) === 'add.php' ? 'active' : '' ?>">+ Add New Card</a>
    <a href="id-card.php" class="<?= basename($_SERVER['PHP_SELF']) === 'id-card.php' ? 'active' : '' ?>">Generate ID Card</a>
  </nav>
  <div class="side-foot">Student / Employee &bull; Core PHP + MySQL</div>
</aside>
<div class="topbar">
  <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')" aria-label="Menu">&#9776;</button>
  <strong><?= e($pageTitle ?? 'Dashboard') ?></strong>
</div>
<main class="main">
