<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/icons.php';
?>
</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-brand">
      <?= icon('shield', 'shield-icon small') ?>
      <span><?= htmlspecialchars($site_name) ?></span>
    </div>

    <nav class="footer-links">
      <?php foreach ($nav_links as $link): ?>
        <a href="<?= htmlspecialchars($link['url']) ?>"<?= !empty($link['external']) ? ' target="_blank" rel="noopener"' : '' ?>>
          <?= htmlspecialchars($link['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <p class="footer-copy">&copy; <?= htmlspecialchars($current_year) ?> All Rights Reserved.</p>
  </div>
</footer>

<script src="assets/js/totp.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
