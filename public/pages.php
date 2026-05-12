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
$pageCount = SiteCatalog::count();
$title = $appName . ' - Library';
$pageTitle = 'Browse the curated ' . $pageCount . '-page website library';
$pageSubtitle = 'The catalog is now more selective, easier to browse, and better aligned with a professional product presentation.';
$grouped = SiteCatalog::grouped();

require __DIR__ . '/partials/header.php';
?>
<section class="content-grid section-stack">
  <?php foreach ($grouped as $group): ?>
    <article class="card category-block">
      <div class="section-heading compact">
        <span class="eyebrow"><?= htmlspecialchars((string) $group['meta']['eyebrow'], ENT_QUOTES, 'UTF-8') ?></span>
        <h2><?= htmlspecialchars((string) $group['meta']['label'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars((string) $group['meta']['description'], ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <div class="card-grid three-col">
        <?php foreach ($group['pages'] as $page): ?>
          <article class="card link-card slim-card">
            <h3><?= htmlspecialchars((string) $page['title'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p><?= htmlspecialchars((string) $page['summary'], ENT_QUOTES, 'UTF-8') ?></p>
            <a class="text-link" href="<?= htmlspecialchars(Url::to('/page.php?slug=' . rawurlencode((string) $page['slug'])), ENT_QUOTES, 'UTF-8') ?>">View detail</a>
          </article>
        <?php endforeach; ?>
      </div>
    </article>
  <?php endforeach; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>