<?php
require_once __DIR__ . '/../config/assets.php';
?>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script src="<?= htmlspecialchars(sizo_asset('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(sizo_asset('assets/js/signup.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(sizo_asset('assets/js/companies.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php if (empty($skipAnalytics)): ?>
  <script src="<?= htmlspecialchars(sizo_asset('assets/js/analytics.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<?php endif; ?>
</body>
</html>
