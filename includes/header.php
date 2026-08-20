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

// Security headers — backs the "modern security headers" claim on the About
// page. No inline <style>/<script> or external origins are used, so this can
// be a strict, no-'unsafe-inline' policy.
$csp_nonce = base64_encode(random_bytes(16));

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header("Content-Security-Policy: default-src 'self'; style-src 'self'; script-src 'self' 'nonce-{$csp_nonce}' https://www.googletagmanager.com; img-src 'self' data: https://www.google-analytics.com https://www.googletagmanager.com; connect-src 'self' https://www.google-analytics.com https://analytics.google.com https://region1.google-analytics.com; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
}

$current_page = $current_page ?? 'home';
$page_title   = $page_title ?? ($site_name . ' — Free 2FA Code Generator');
$page_desc    = $page_desc ?? 'A free, secure, privacy-focused two-factor authentication (TOTP) code generator that works entirely in your browser.';

$canonical_path = ($current_page === 'about') ? '/about' : '/';
$canonical_url  = $site_url . $canonical_path;
$og_image       = $site_url . '/assets/img/og-image.php';

// JSON-LD — use json_encode so special chars are safe valid JSON
$jsonld = json_encode([
    '@context'            => 'https://schema.org',
    '@type'               => 'WebApplication',
    'name'                => $site_name,
    'url'                 => $site_url,
    'description'         => $page_desc,
    'applicationCategory' => 'SecurityApplication',
    'operatingSystem'     => 'Any',
    'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
    'featureList'         => [
        'Generate TOTP 2FA codes from secret keys',
        'Works entirely offline in the browser',
        'PIN-encrypted local storage',
        'No data sent to any server',
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#2563eb">
<link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
<meta name="google-site-verification" content="IlFyBSiKPS-tM075fQRnDODvVrR6Vg1W-gswPJiD9Y8" />

<!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">

<!-- Open Graph -->
<meta property="og:type"        content="website">
<meta property="og:locale"      content="en_US">
<meta property="og:url"         content="<?= htmlspecialchars($canonical_url) ?>">
<meta property="og:title"       content="<?= htmlspecialchars($page_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
<meta property="og:image"       content="<?= htmlspecialchars($og_image) ?>">
<meta property="og:image:width"  content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name"   content="<?= htmlspecialchars($site_name) ?>">

<!-- Twitter Card -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="<?= htmlspecialchars($page_title) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($page_desc) ?>">
<meta name="twitter:image"       content="<?= htmlspecialchars($og_image) ?>">

<!-- JSON-LD Structured Data -->
<script type="application/ld+json"><?= $jsonld ?></script>

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1EHPNQ28FC"></script>
<script nonce="<?= htmlspecialchars($csp_nonce) ?>">
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-1EHPNQ28FC');
</script>

<link rel="stylesheet" href="/assets/css/style.css">
<script nonce="<?= htmlspecialchars($csp_nonce) ?>">
  try { if (localStorage.getItem('tfa_theme') === 'dark') document.documentElement.setAttribute('data-theme', 'dark'); } catch(e) {}
</script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
  <div class="container header-inner">
    <a href="/" class="brand">
      <?= icon('shield', 'shield-icon') ?>
      <span><?= htmlspecialchars($site_name) ?></span>
    </a>

    <nav class="desktop-nav" aria-label="Main navigation">
      <?php foreach ($nav_links as $link):
        $is_active = ($link['url'] === '/' && $current_page === 'home')
                  || ($link['url'] === '/about' && $current_page === 'about');
      ?>
        <a href="<?= htmlspecialchars($link['url']) ?>"
           class="nav-link<?= $is_active ? ' active' : '' ?>"
           <?= !empty($link['external']) ? 'target="_blank" rel="noopener"' : '' ?>>
          <?= htmlspecialchars($link['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="header-actions">
      <?php if ($current_page === 'home'): ?>
        <button id="historyBtn" class="btn btn-outline btn-sm header-only-desktop" type="button">
          <?= icon('history', 'icon-sm') ?><span>History (<span id="historyCount">0</span>)</span>
        </button>
        <button id="refreshBtn" class="btn btn-outline btn-sm header-only-desktop" type="button">
          <?= icon('refresh', 'icon-sm') ?><span>Refresh</span>
        </button>
      <?php endif; ?>

      <button id="themeToggle" class="icon-btn" type="button" aria-label="Toggle light/dark theme">
        <?= icon('sun', 'icon icon-sun') ?>
        <?= icon('moon', 'icon icon-moon') ?>
      </button>

      <?php if ($current_page === 'about'): ?>
        <a href="/" class="btn btn-outline btn-sm">
          <?= icon('arrow-left', 'icon-sm') ?><span>Back</span>
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
      <?php if ($current_page === 'home'): ?>
        <button type="button" id="mobileHistoryBtn">
          <?= icon('history', 'icon-sm') ?><span>History (<span id="mobileHistoryCount">0</span>)</span>
        </button>
        <button type="button" id="mobileRefreshBtn">
          <?= icon('refresh', 'icon-sm') ?><span>Refresh Codes</span>
        </button>
      <?php endif; ?>
    </div>
  </nav>
</header>

<main id="main">
