<?php
declare(strict_types=1);

use App\Auth\AuthService;
use App\Config\Env;
use App\Content\SiteCatalog;
use App\Support\Flash;
use App\Support\Url;

require dirname(__DIR__) . '/bootstrap/app.php';

$auth = new AuthService();
$user = $auth->user();
$appName = (string) Env::get('APP_NAME', 'Black Hole AI Pro');
$flash = Flash::get();
$title = $appName . ' - Solutions';
$pageTitle = 'Solutions that turn AI into actual business output';
$pageSubtitle = 'From support to sales to executive reporting, these solution pages give the website commercial depth.';
$pages = SiteCatalog::byCategory('solutions');

require __DIR__ . '/partials/header.php';
?>
<section class="content-grid two-col editorial-grid">
  <article class="card feature-card">
    <span class="eyebrow">Commercial layer</span>
    <h2>Solution pages that make the site feel market-ready</h2>
    <p>Each solution page tells a different buying story. That gives the website more than visual polish; it helps address distinct client needs with clear positioning.</p>
  </article>
  <article class="card stat-card premium-card">
    <h3>Inside this section</h3>
    <ul class="feature-list">
      <li>Support, sales, marketing, HR, finance, and legal use cases</li>
      <li>Premium copy structure for services and product positioning</li>
      <li>Detail pages that can later connect to live demos or forms</li>
    </ul>
  </article>
</section>
<section class="content-grid three-col">
  <?php foreach ($pages as $page): ?>
    <article class="card link-card slim-card">
      <span class="eyebrow">Solution</span>
      <h3><?= htmlspecialchars((string) $page['title'], ENT_QUOTES, 'UTF-8') ?></h3>
      <p><?= htmlspecialchars((string) $page['summary'], ENT_QUOTES, 'UTF-8') ?></p>
      <a class="text-link" href="<?= htmlspecialchars(Url::to('/page.php?slug=' . rawurlencode((string) $page['slug'])), ENT_QUOTES, 'UTF-8') ?>">Open detail</a>
    </article>
  <?php endforeach; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
