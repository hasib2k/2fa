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

<div class="page-grid container">

  <!-- ── Main column: generator + how-to ── -->
  <div class="page-main">

    <section class="generator">
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

      <div class="backup-row">
        <button id="exportBackupBtn" class="btn btn-outline" type="button">
          <?= icon('download', 'icon-sm') ?><span>Export Backup</span>
        </button>
        <button id="importBackupBtn" class="btn btn-outline" type="button">
          <?= icon('upload', 'icon-sm') ?><span>Import Backup</span>
        </button>
        <input type="file" id="importFileInput" accept="application/json" hidden>
      </div>
    </section>

    <section class="howto card">
      <h2>How to Use</h2>
      <ul>
        <li>Enter a name for your account (optional)</li>
        <li>Paste the secret key from your 2FA setup</li>
        <li>Your code will generate automatically every 30 seconds</li>
        <li>Click the code to copy it to your clipboard</li>
        <li>Add multiple accounts as needed</li>
        <li>The first time you save a key, you'll set a PIN to encrypt it</li>
        <li>Your keys are saved locally, PIN-encrypted, for 7 days</li>
        <li>Export a backup before clearing your browser, switching devices, or if 7 days isn't long enough</li>
      </ul>
    </section>

  </div><!-- /.page-main -->

  <!-- ── Sidebar: sponsor ── -->
  <aside class="page-sidebar">

    <section id="sponsor" class="sponsor-section">
      <div class="sponsor-head">
        <h2><?= icon($sponsor['eyebrow_icon'], 'sponsor-head-icon') ?><span><?= htmlspecialchars($sponsor['eyebrow']) ?></span></h2>
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
              <?= icon($f['icon'], 'sponsor-feature-icon') ?>
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

  </aside><!-- /.page-sidebar -->

</div><!-- /.page-grid -->

<div id="lockScreen" class="overlay" hidden>
  <div class="overlay-card">
    <?= icon('lock', 'shield-icon') ?>
    <h2>Enter Your PIN</h2>
    <p class="overlay-sub">Your saved keys are encrypted. Enter your PIN to unlock them.</p>
    <input type="password" id="lockPinInput" class="pin-input" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="••••" autocomplete="off">
    <p class="overlay-error" id="lockError" hidden>Incorrect PIN. Try again.</p>
    <button type="button" id="lockUnlockBtn" class="btn btn-primary btn-block">Unlock</button>
    <button type="button" id="lockResetBtn" class="btn btn-outline btn-danger btn-block">Forgot PIN? Reset All Data</button>
  </div>
</div>

<div id="pinSetupModal" class="overlay" hidden>
  <div class="overlay-card">
    <?= icon('lock', 'shield-icon') ?>
    <h2>Set a PIN</h2>
    <p class="overlay-sub">Choose a 4–6 digit PIN to encrypt your secret keys on this device. There's no way to recover it if you forget it.</p>
    <input type="password" id="setupPinInput" class="pin-input" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="New PIN" autocomplete="off">
    <input type="password" id="setupPinConfirm" class="pin-input" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="Confirm PIN" autocomplete="off">
    <p class="overlay-error" id="setupError" hidden></p>
    <button type="button" id="setupConfirmBtn" class="btn btn-primary btn-block">Set PIN &amp; Save</button>
    <button type="button" id="setupCancelBtn" class="btn btn-outline btn-block">Cancel</button>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
