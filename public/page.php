<?php
declare(strict_types=1);

use App\Auth\AuthService;
use App\Config\Env;
use App\Content\SiteCatalog;
use App\Support\Flash;
use App\Support\Url;

require dirname(__DIR__) . '/bootstrap/app.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$page = SiteCatalog::find($slug);
if ($page === null) {
    http_response_code(404);
}

$auth = new AuthService();
$user = $auth->user();
$appName = (string) Env::get('APP_NAME', 'Black Hole AI Pro');
$flash = Flash::get();
$title = $appName . ' - ' . ($page['title'] ?? 'Page Not Found');
$pageTitle = $page['title'] ?? 'Page not found';
$pageSubtitle = $page['hero'] ?? 'The requested page could not be found in the website library.';
$related = $page !== null ? SiteCatalog::related((string) $page['slug'], 3) : [];

require __DIR__ . '/partials/header.php';
?>
<?php if ($page === null): ?>
  <section class="content-grid form-centered">
    <article class="card feature-card">
      <span class="eyebrow">Missing page</span>
      <h2>The page you requested does not exist.</h2>
      <p>Return to the library and pick another destination from the professional content catalog.</p>
      <div class="action-row">
        <a class="button primary" href="<?= htmlspecialchars(Url::to('/pages.php'), ENT_QUOTES, 'UTF-8') ?>">Open library</a>
        <a class="button secondary" href="<?= htmlspecialchars(Url::to('/index.php'), ENT_QUOTES, 'UTF-8') ?>">Go home</a>
      </div>
    </article>
  </section>
<?php else: ?>
  <section class="content-grid detail-layout">
    <article class="card detail-panel">
      <span class="eyebrow"><?= htmlspecialchars((string) $page['eyebrow'], ENT_QUOTES, 'UTF-8') ?></span>
      <h2><?= htmlspecialchars((string) $page['title'], ENT_QUOTES, 'UTF-8') ?></h2>
      <p><?= htmlspecialchars((string) $page['summary'], ENT_QUOTES, 'UTF-8') ?></p>
      <div class="metric-strip">
        <?php foreach ($page['metrics'] as $metric): ?>
          <div class="mini-stat">
            <strong><?= htmlspecialchars((string) $metric['value'], ENT_QUOTES, 'UTF-8') ?></strong>
            <span><?= htmlspecialchars((string) $metric['label'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </article>

    <aside class="card stat-card">
      <h3>Key highlights</h3>
      <ul class="feature-list">
        <?php foreach ($page['highlights'] as $highlight): ?>
          <li><?= htmlspecialchars((string) $highlight, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    </aside>
  </section>

  <section class="content-grid three-col">
    <?php foreach ($page['sections'] as $section): ?>
      <article class="card mini-card">
        <span class="eyebrow"><?= htmlspecialchars((string) $page['category_label'], ENT_QUOTES, 'UTF-8') ?></span>
        <h3><?= htmlspecialchars((string) $section['title'], ENT_QUOTES, 'UTF-8') ?></h3>
        <p><?= htmlspecialchars((string) $section['content'], ENT_QUOTES, 'UTF-8') ?></p>
        <ul class="feature-list tight-list">
          <?php foreach ($section['bullets'] as $bullet): ?>
            <li><?= htmlspecialchars((string) $bullet, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </article>
    <?php endforeach; ?>
  </section>

  <section class="content-grid section-stack">
    <div class="section-heading compact">
      <span class="eyebrow">Related pages</span>
      <h2>Continue exploring the same category</h2>
    </div>
    <div class="card-grid three-col">
      <?php foreach ($related as $item): ?>
        <article class="card link-card slim-card">
          <h3><?= htmlspecialchars((string) $item['title'], ENT_QUOTES, 'UTF-8') ?></h3>
          <p><?= htmlspecialchars((string) $item['summary'], ENT_QUOTES, 'UTF-8') ?></p>
          <a class="text-link" href="<?= htmlspecialchars(Url::to('/page.php?slug=' . rawurlencode((string) $item['slug'])), ENT_QUOTES, 'UTF-8') ?>">Read more</a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
