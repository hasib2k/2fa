<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/icons.php';

$page_title   = 'Free 2FA Code Generator — ' . $site_name;
$page_desc    = 'Generate TOTP two-factor authentication codes instantly from your secret keys. Secure, fast, and works offline in your browser.';
$current_page = 'home';

require __DIR__ . '/includes/header.php';
?>

<section class="hero container">
  <h1>Free 2FA Code<br>Generator</h1>
  <p class="hero-sub">Generate TOTP codes instantly from your secret keys</p>
  <p class="hero-sub2">Secure, fast, and works offline in your browser</p>
  <button id="clearHistoryBtn" class="btn btn-outline btn-danger" type="button">
    <?= icon('trash', 'icon-sm') ?><span>Clear History</span>
  </button>
</section>

<section id="sponsor" class="sponsor-section container">
  <div class="sponsor-head">
    <h2><?= htmlspecialchars($sponsor['eyebrow']) ?></h2>
    <span class="pill pill-gray"><?= htmlspecialchars($sponsor['badge']) ?></span>
  </div>

  <div class="sponsor-card">
    <div class="sponsor-partner-row">
      <strong><?= htmlspecialchars($sponsor['partner']) ?></strong>
      <span class="pill pill-blue"><?= htmlspecialchars($sponsor['tag']) ?></span>
    </div>
    <p class="sponsor-headline"><?= htmlspecialchars($sponsor['headline']) ?></p>
    <div class="sponsor-body"><?= htmlspecialchars($sponsor['body']) ?></div>

    <div class="sponsor-features">
      <?php foreach ($sponsor['features'] as $f): ?>
        <div class="sponsor-feature">
          <span class="emoji"><?= htmlspecialchars($f['icon']) ?></span>
          <h3><?= htmlspecialchars($f['title']) ?></h3>
          <p><?= htmlspecialchars($f['desc']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <a href="<?= htmlspecialchars($sponsor['cta_url']) ?>" class="btn btn-primary btn-block">
      <?= htmlspecialchars($sponsor['cta_label']) ?>
    </a>

    <div class="sponsor-trust">
      <?php foreach ($sponsor['trust'] as $t): ?>
        <span class="pill pill-green"><?= htmlspecialchars($t) ?></span>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="generator container">
  <div id="accountsList"></div>

  <div class="timer-card card">
    <?= icon('stopwatch') ?>
    <div>
      <p id="timerText">30s until next refresh</p>
      <div class="progress-track"><div id="progressBar" class="progress-fill"></div></div>
    </div>
  </div>

  <button id="addAccountBtn" class="btn btn-dark btn-block" type="button">
    <?= icon('plus', 'icon-sm') ?><span>Add Another Account</span>
  </button>
</section>

<section class="howto card">
  <h2>How to Use</h2>
  <ul>
    <li>Enter a name for your account (optional)</li>
    <li>Paste the secret key from your 2FA setup</li>
    <li>Your code will generate automatically every 30 seconds</li>
    <li>Click the code to copy it to your clipboard</li>
    <li>Add multiple accounts as needed</li>
    <li>Your keys are saved locally for 7 days</li>
  </ul>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
