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
$categoryCount = count(SiteCatalog::categories());
$title = $appName . ' - Home';
$pageTitle = 'A sharper AI website with a cleaner product story';
$pageSubtitle = 'Black Hole AI Pro now ships with secure auth, protected chat, and a curated ' . $pageCount . '-page web experience designed to feel more focused and professional.';
$featuredPages = SiteCatalog::featured(8);
$latestPages = SiteCatalog::latest(6);
$grouped = SiteCatalog::grouped();

require __DIR__ . '/partials/header.php';
?>
<section class="content-grid hero-split">
  <article class="card hero-panel">
    <span class="eyebrow">Launch system</span>
    <h2>Professional home page up front. Secure AI workspace behind it.</h2>
    <p>The website now feels cleaner, more premium, and more focused. The experience highlights the core product story first, while the AI workspace stays ready behind a polished business-style shell.</p>
    <div class="action-row">
      <a class="button primary" href="<?= htmlspecialchars(Url::to('/pages.php'), ENT_QUOTES, 'UTF-8') ?>">Explore <?= htmlspecialchars((string) $pageCount, ENT_QUOTES, 'UTF-8') ?> Pages</a>
      <?php if (is_array($user)): ?>
        <a class="button subtle-button" href="<?= htmlspecialchars(Url::to('/chat.php'), ENT_QUOTES, 'UTF-8') ?>">Open Chat Workspace</a>
      <?php else: ?>
        <a class="button subtle-button" href="<?= htmlspecialchars(Url::to('/signup.php'), ENT_QUOTES, 'UTF-8') ?>">Create Account</a>
      <?php endif; ?>
    </div>
  </article>

  <aside class="card stat-card premium-card hero-visual-card">
    <span class="eyebrow">At a glance</span>
    <div class="metric-stack">
      <div>
        <strong><?= htmlspecialchars((string) $pageCount, ENT_QUOTES, 'UTF-8') ?></strong>
        <span>Curated content pages</span>
      </div>
      <div>
        <strong><?= htmlspecialchars((string) $categoryCount, ENT_QUOTES, 'UTF-8') ?></strong>
        <span>Website categories</span>
      </div>
      <div>
        <strong>1</strong>
        <span>Protected AI chat workspace</span>
      </div>
    </div>
    <div class="hero-cinematic" aria-hidden="true">
      <div class="hero-cinematic-stage">
        <span class="hero-scanline"></span>
        <span class="hero-ring hero-ring-one"></span>
        <span class="hero-ring hero-ring-two"></span>
        <span class="hero-ring hero-ring-three"></span>
        <div class="hero-logo-core" aria-hidden="true"></div>
        <div class="hero-signal hero-signal-left">
          <strong>Black Hole V1.3</strong>
          <span>Live AI workspace</span>
        </div>
        <div class="hero-signal hero-signal-right">
          <strong>Web and chat</strong>
          <span>Motion-rich interface</span>
        </div>
      </div>
      <div class="hero-film-strip">
        <article class="hero-film-card">
          <span>Realtime feel</span>
          <strong>Luxury black and gold motion</strong>
        </article>
        <article class="hero-film-card">
          <span>Developer</span>
          <strong>Designed by Ayat Rahman</strong>
        </article>
      </div>
    </div>
  </aside>
</section>

<section class="content-grid three-col metric-band">
  <article class="card mini-card">
    <span class="eyebrow">System</span>
    <h3>Curated information architecture</h3>
    <p>The page library is tighter and easier to explore, which gives visitors a clearer path through the product.</p>
  </article>
  <article class="card mini-card">
    <span class="eyebrow">Design</span>
    <h3>Polished header and footer</h3>
    <p>The header and footer now feel structured, branded, and ready for a serious product website.</p>
  </article>
  <article class="card mini-card">
    <span class="eyebrow">Growth</span>
    <h3>Stable foundation for next modules</h3>
    <p>Forms, dashboards, analytics, admin tools, and saved chats can now be added on top of a cleaner foundation.</p>
  </article>
</section>

<section class="content-grid section-stack">
  <div class="section-heading">
    <span class="eyebrow">Featured destinations</span>
    <h2>High-value pages across the focused website library</h2>
  </div>
  <div class="card-grid four-col">
    <?php foreach ($featuredPages as $page): ?>
      <article class="card link-card">
        <span class="eyebrow"><?= htmlspecialchars((string) $page['category_label'], ENT_QUOTES, 'UTF-8') ?></span>
        <h3><?= htmlspecialchars((string) $page['title'], ENT_QUOTES, 'UTF-8') ?></h3>
        <p><?= htmlspecialchars((string) $page['summary'], ENT_QUOTES, 'UTF-8') ?></p>
        <a class="text-link" href="<?= htmlspecialchars(Url::to('/page.php?slug=' . rawurlencode((string) $page['slug'])), ENT_QUOTES, 'UTF-8') ?>">Open page</a>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="content-grid section-stack">
  <div class="section-heading">
    <span class="eyebrow">Architecture</span>
    <h2>Category-based browsing with less clutter</h2>
  </div>
  <div class="card-grid four-col">
    <?php foreach ($grouped as $group): ?>
      <article class="card category-card">
        <span class="eyebrow"><?= htmlspecialchars((string) $group['meta']['eyebrow'], ENT_QUOTES, 'UTF-8') ?></span>
        <h3><?= htmlspecialchars((string) $group['meta']['label'], ENT_QUOTES, 'UTF-8') ?></h3>
        <p><?= htmlspecialchars((string) $group['meta']['description'], ENT_QUOTES, 'UTF-8') ?></p>
        <div class="tag-row">
          <span class="tag"><?= htmlspecialchars((string) count($group['pages']), ENT_QUOTES, 'UTF-8') ?> pages</span>
          <span class="tag">Focused layout</span>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="content-grid two-col editorial-grid">
  <article class="card feature-card">
    <span class="eyebrow">Latest additions</span>
    <h2>Fresh pages inside the refined content system</h2>
    <div class="list-stack">
      <?php foreach ($latestPages as $page): ?>
        <a class="list-link" href="<?= htmlspecialchars(Url::to('/page.php?slug=' . rawurlencode((string) $page['slug'])), ENT_QUOTES, 'UTF-8') ?>">
          <strong><?= htmlspecialchars((string) $page['title'], ENT_QUOTES, 'UTF-8') ?></strong>
          <span><?= htmlspecialchars((string) $page['category_label'], ENT_QUOTES, 'UTF-8') ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </article>

  <article class="card callout-card">
    <span class="eyebrow">Workspace</span>
    <h2>The AI chat is still the working core</h2>
    <p>The public website builds trust first, and the protected chat workspace delivers the real utility after login. Together they now feel more balanced and intentional.</p>
    <div class="action-row">
      <?php if (is_array($user)): ?>
        <a class="button primary" href="<?= htmlspecialchars(Url::to('/chat.php'), ENT_QUOTES, 'UTF-8') ?>">Continue to Chat</a>
      <?php else: ?>
        <a class="button primary" href="<?= htmlspecialchars(Url::to('/login.php'), ENT_QUOTES, 'UTF-8') ?>">Login to Access Chat</a>
      <?php endif; ?>
      <a class="button subtle-button" href="<?= htmlspecialchars(Url::to('/contact.php'), ENT_QUOTES, 'UTF-8') ?>">Request Project Setup</a>
    </div>
  </article>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
