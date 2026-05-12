<?php
$pageCount = $pageCount ?? \App\Content\SiteCatalog::count();
$categoryCount = $categoryCount ?? count(\App\Content\SiteCatalog::categories());
$appName = $appName ?? 'Black Hole AI Pro';
$user = $user ?? null;
?>
    <footer class="site-footer">
      <div class="footer-top">
        <section class="footer-brand">
          <span class="eyebrow">Platform footer</span>
          <h3><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></h3>
          <p>A professional PHP AI product shell with a cleaner page library, secure member access, and a protected chat workspace for serious builds.</p>
          <div class="footer-badges">
            <span class="footer-chip"><?= htmlspecialchars((string) $pageCount, ENT_QUOTES, 'UTF-8') ?> curated pages</span>
            <span class="footer-chip"><?= htmlspecialchars((string) $categoryCount, ENT_QUOTES, 'UTF-8') ?> focused sections</span>
            <span class="footer-chip">SQLite and AI chat</span>
          </div>
          <div class="footer-credit-box">
            <strong>Ayat Rahman</strong>
            <span>Designed and developed by Ayat Rahman for the full Black Hole web experience.</span>
            <a class="footer-credit-link" href="https://www.instagram.com/ayat_rahman7690/" target="_blank" rel="noopener noreferrer">@ayat_rahman7690</a>
          </div>
        </section>

        <div class="footer-grid">
          <section class="footer-section">
            <h3>Platform</h3>
            <div class="footer-links">
              <a href="<?= htmlspecialchars(\App\Support\Url::to('/index.php'), ENT_QUOTES, 'UTF-8') ?>">Home</a>
              <a href="<?= htmlspecialchars(\App\Support\Url::to('/pages.php'), ENT_QUOTES, 'UTF-8') ?>">Curated library</a>
              <a href="<?= htmlspecialchars(\App\Support\Url::to('/solutions.php'), ENT_QUOTES, 'UTF-8') ?>">Solutions</a>
              <a href="<?= htmlspecialchars(\App\Support\Url::to('/resources.php'), ENT_QUOTES, 'UTF-8') ?>">Resources</a>
            </div>
          </section>

          <section class="footer-section">
            <h3>Company</h3>
            <div class="footer-links">
              <a href="<?= htmlspecialchars(\App\Support\Url::to('/about.php'), ENT_QUOTES, 'UTF-8') ?>">About</a>
              <a href="<?= htmlspecialchars(\App\Support\Url::to('/pricing.php'), ENT_QUOTES, 'UTF-8') ?>">Pricing</a>
              <a href="<?= htmlspecialchars(\App\Support\Url::to('/contact.php'), ENT_QUOTES, 'UTF-8') ?>">Contact</a>
              <a href="<?= htmlspecialchars(\App\Support\Url::to('/industries.php'), ENT_QUOTES, 'UTF-8') ?>">Industries</a>
            </div>
          </section>

          <section class="footer-section footer-cta">
            <h3>Next step</h3>
            <p>Use the curated site to build trust first, then move into the secure workspace or project inquiry flow.</p>
            <div class="action-row">
              <?php if (is_array($user)): ?>
                <a class="button primary" href="<?= htmlspecialchars(\App\Support\Url::to('/chat.php'), ENT_QUOTES, 'UTF-8') ?>">Open Chat</a>
              <?php else: ?>
                <a class="button primary" href="<?= htmlspecialchars(\App\Support\Url::to('/signup.php'), ENT_QUOTES, 'UTF-8') ?>">Create Account</a>
              <?php endif; ?>
              <a class="button subtle-button" href="<?= htmlspecialchars(\App\Support\Url::to('/contact.php'), ENT_QUOTES, 'UTF-8') ?>">Request Setup</a>
            </div>
          </section>
        </div>
      </div>

      <div class="footer-bottom">
        <p><?= htmlspecialchars((string) date('Y'), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?> / Designed and developed by Ayat Rahman / AI, web, and interface engineering</p>
        <div class="footer-bottom-links">
          <a href="<?= htmlspecialchars(\App\Support\Url::to('/login.php'), ENT_QUOTES, 'UTF-8') ?>">Login</a>
          <a href="<?= htmlspecialchars(\App\Support\Url::to('/signup.php'), ENT_QUOTES, 'UTF-8') ?>">Sign Up</a>
          <a href="<?= htmlspecialchars(\App\Support\Url::to('/contact.php'), ENT_QUOTES, 'UTF-8') ?>">Contact</a>
        </div>
      </div>
    </footer>
  </main>
</body>
</html>
