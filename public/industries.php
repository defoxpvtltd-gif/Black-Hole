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
$title = $appName . ' - Industries';
$pageTitle = 'Industry pages for trust, targeting, and niche messaging';
$pageSubtitle = 'These pages give the website stronger market segmentation so visitors can see where the product fits.';
$pages = SiteCatalog::byCategory('industries');

require __DIR__ . '/partials/header.php';
?>
<section class="content-grid two-col editorial-grid">
  <article class="card feature-card">
    <span class="eyebrow">Industry fit</span>
    <h2>Different sectors need different language, proof, and operating models</h2>
    <p>Industry-specific pages make the website feel more professional because each visitor can understand the product in the context of their own market.</p>
  </article>
  <article class="card stat-card premium-card">
    <h3>Coverage</h3>
    <ul class="feature-list">
      <li>SaaS, healthcare, banking, logistics, media, and public sector</li>
      <li>Sector-aware positioning without duplicating the whole design system</li>
      <li>Reusable layout blocks that keep the site maintainable</li>
    </ul>
  </article>
</section>
<section class="content-grid three-col">
  <?php foreach ($pages as $page): ?>
    <article class="card link-card slim-card">
      <span class="eyebrow">Industry</span>
      <h3><?= htmlspecialchars((string) $page['title'], ENT_QUOTES, 'UTF-8') ?></h3>
      <p><?= htmlspecialchars((string) $page['summary'], ENT_QUOTES, 'UTF-8') ?></p>
      <a class="text-link" href="<?= htmlspecialchars(Url::to('/page.php?slug=' . rawurlencode((string) $page['slug'])), ENT_QUOTES, 'UTF-8') ?>">Open detail</a>
    </article>
  <?php endforeach; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
