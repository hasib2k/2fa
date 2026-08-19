<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/icons.php';

$page_title   = 'About — ' . $site_name;
$page_desc    = 'Learn how ' . $site_name . ' generates two-factor authentication codes privately and securely, entirely in your browser.';
$current_page = 'about';

require __DIR__ . '/includes/header.php';
?>

<section class="about-hero container">
  <?= icon('shield-outline', 'shield-icon') ?>
  <h1>About <?= htmlspecialchars($site_name) ?></h1>
  <p>A free, secure, and privacy-focused two-factor authentication code generator that works entirely in your browser.</p>
</section>

<section class="section-stack">

  <div class="card mission-card">
    <h2>Our Mission</h2>
    <p>We believe that everyone deserves access to secure, reliable two-factor authentication tools without compromising
      their privacy or paying expensive fees. <?= htmlspecialchars($site_name) ?> was created to provide a simple,
      trustworthy solution that puts your security and privacy first.</p>
  </div>

  <div class="feature-card green">
    <?= icon('lock', 'feature-icon') ?>
    <h3>Privacy First</h3>
    <p>All your secret keys are stored locally on your device. Nothing is sent to our servers, ensuring your sensitive
      information never leaves your control.</p>
    <span class="pill">100% Local Storage</span>
  </div>

  <div class="feature-card blue">
    <?= icon('globe', 'feature-icon') ?>
    <h3>Works Offline</h3>
    <p>Once loaded, the generator works without an internet connection. Perfect for travel, areas with poor connectivity,
      or when you need maximum security.</p>
    <span class="pill">No Internet Required</span>
  </div>

  <div class="feature-card purple">
    <?= icon('phone', 'feature-icon') ?>
    <h3>Universal Compatibility</h3>
    <p>Generates standard TOTP codes that work with Google Authenticator, Authy, Microsoft Authenticator, and any other
      RFC 6238 compliant application.</p>
    <span class="pill">RFC 6238 Standard</span>
  </div>

  <div class="feature-card orange">
    <?= icon('shield-outline', 'feature-icon') ?>
    <h3>Open Source Security</h3>
    <p>Built with transparency in mind. Our code uses industry-standard cryptographic libraries and follows security
      best practices you can verify.</p>
    <span class="pill">Transparent &amp; Secure</span>
  </div>

  <div class="card">
    <h2 style="margin:0 0 16px;font-size:1.2rem;">How It Works</h2>
    <div class="howto-steps">
      <div class="howto-step">
        <span class="step-num">1</span>
        <div>
          <h3>Enter Your Secret Key</h3>
          <p>Paste the secret key provided by your service (Google, GitHub, etc.) when setting up 2FA.</p>
        </div>
      </div>
      <div class="howto-step">
        <span class="step-num">2</span>
        <div>
          <h3>Automatic Code Generation</h3>
          <p>Our algorithm generates a new 6-digit code every 30 seconds using the TOTP standard.</p>
        </div>
      </div>
      <div class="howto-step">
        <span class="step-num">3</span>
        <div>
          <h3>Use Your Code</h3>
          <p>Click to copy the code and paste it into your login form. Codes refresh automatically.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="security-card">
    <h2>Security &amp; Privacy</h2>
    <p class="security-item"><strong>Local Storage Only:</strong> Your secret keys are stored in your browser's local
      storage and automatically deleted after 7 days. They never leave your device.</p>
    <p class="security-item"><strong>No Analytics:</strong> We don't track your usage, collect personal data, or use
      cookies for tracking purposes.</p>
    <p class="security-item"><strong>HTTPS Only:</strong> All connections are encrypted and we use modern security
      headers to protect against common web vulnerabilities.</p>
    <p class="security-item"><strong>Standard Algorithms:</strong> We use the same cryptographic standards
      (HMAC-SHA1, Base32) as major authenticator apps.</p>
  </div>

  <div class="card faq-card">
    <h2>Frequently Asked Questions</h2>

    <div class="faq-item">
      <h3>Is this safe to use?</h3>
      <p>Yes! Your secret keys never leave your device. We use the same security standards as Google Authenticator
        and other trusted apps.</p>
    </div>
    <div class="faq-item">
      <h3>What if I lose my secret keys?</h3>
      <p>Since everything is stored locally, clearing your browser data will remove saved keys. Always keep backup
        codes from your services.</p>
    </div>
    <div class="faq-item">
      <h3>Does this work on mobile?</h3>
      <p>The site is fully responsive and works great on phones and tablets.</p>
    </div>
    <div class="faq-item" id="contact">
      <h3>Is this free?</h3>
      <p>Yes, completely free with no hidden costs, premium features, or subscription plans.</p>
    </div>
  </div>

</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
