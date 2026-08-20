<?php
/**
 * Shared page header.
 * Expects (optionally set by the calling page before include):
 *   $page_title   - <title> text
 *   $page_desc    - meta description
 *   $current_page - 'home' | 'about' (controls the right-side header button)
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/icons.php';

$current_page = $current_page ?? 'home';
$page_title   = $page_title ?? ($site_name . ' — Free 2FA Code Generator');
$page_desc    = $page_desc ?? 'A free, secure, privacy-focused two-factor authentication (TOTP) code generator that works entirely in your browser.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
<link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
  <div class="container header-inner">
    <a href="index.php" class="brand">
      <?= icon('shield', 'shield-icon') ?>
      <span><?= htmlspecialchars($site_name) ?></span>
    </a>

    <nav class="desktop-nav" aria-label="Main navigation">
      <?php foreach ($nav_links as $link):
        $is_active = ($link['url'] === 'index.php' && $current_page === 'home')
                  || ($link['url'] === 'about.php' && $current_page === 'about');
      ?>
        <a href="<?= htmlspecialchars($link['url']) ?>"
           class="nav-link<?= $is_active ? ' active' : '' ?>"
           <?= !empty($link['external']) ? 'target="_blank" rel="noopener"' : '' ?>>
          <?= htmlspecialchars($link['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="header-actions">
      <button id="themeToggle" class="icon-btn" type="button" aria-label="Toggle light/dark theme">
        <?= icon('sun', 'icon icon-sun') ?>
        <?= icon('moon', 'icon icon-moon') ?>
      </button>

      <?php if ($current_page === 'about'): ?>
        <a href="index.php" class="btn btn-outline btn-sm">
          <?= icon('arrow-left', 'icon-sm') ?><span>Back to Generator</span>
        </a>
      <?php else: ?>
        <button id="menuToggle" class="icon-btn" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
          <?= icon('menu', 'icon icon-menu') ?>
          <?= icon('close', 'icon icon-close') ?>
        </button>
      <?php endif; ?>
    </div>
  </div>

  <nav id="mobileMenu" class="mobile-menu" hidden>
    <div class="container mobile-menu-inner">
      <?php foreach ($nav_links as $link): ?>
        <a href="<?= htmlspecialchars($link['url']) ?>"<?= !empty($link['external']) ? ' target="_blank" rel="noopener"' : '' ?>>
          <?= htmlspecialchars($link['label']) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </nav>
</header>

<main id="main">
