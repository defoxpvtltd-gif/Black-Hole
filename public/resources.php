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
$title = $appName . ' - Resources';
$pageTitle = 'Resources that make the product feel established';
$pageSubtitle = 'Guides, academy content, templates, webinars, and docs give the site a much more complete footprint.';
$pages = array_merge(SiteCatalog::byCategory('resources'), SiteCatalog::byCategory('documentation'));

require __DIR__ . '/partials/header.php';
?>
<section class="content-grid two-col editorial-grid">
  <article class="card feature-card">
    <span class="eyebrow">Proof and education</span>
    <h2>Resource depth builds confidence before anyone books a call</h2>
    <p>Resources, documentation, and learning pages guide visitors with clarity. This section strengthens authority and builds long-term trust in the product.</p>
  </article>
  <article class="card stat-card premium-card">
    <h3>Included here</h3>
    <ul class="feature-list">
      <li>Templates, playbooks, case studies, academy, and webinars</li>
      <li>Developer-facing implementation pages for technical audiences</li>
      <li>Clear next-step links into login, contact, or AI workspace</li>
    </ul>
  </article>
</section>
<section class="content-grid three-col">
  <?php foreach ($pages as $page): ?>
    <article class="card link-card slim-card">
      <span class="eyebrow"><?= htmlspecialchars((string) $page['category_label'], ENT_QUOTES, 'UTF-8') ?></span>
      <h3><?= htmlspecialchars((string) $page['title'], ENT_QUOTES, 'UTF-8') ?></h3>
      <p><?= htmlspecialchars((string) $page['summary'], ENT_QUOTES, 'UTF-8') ?></p>
      <a class="text-link" href="<?= htmlspecialchars(Url::to('/page.php?slug=' . rawurlencode((string) $page['slug'])), ENT_QUOTES, 'UTF-8') ?>">Open detail</a>
    </article>
  <?php endforeach; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
