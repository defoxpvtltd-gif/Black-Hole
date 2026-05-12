<?php
declare(strict_types=1);

use App\Auth\AuthService;
use App\Config\Env;
use App\Support\Flash;
use App\Support\Url;

require dirname(__DIR__) . '/bootstrap/app.php';

$auth = new AuthService();
$user = $auth->user();
$appName = (string) Env::get('APP_NAME', 'Black Hole AI Pro');
$flash = Flash::get();
$title = $appName . ' - About';
$pageTitle = 'A premium AI brand built on a practical PHP foundation';
$pageSubtitle = 'Black Hole AI Pro combines luxury presentation with real architecture: auth, database, pages, and protected AI chat.';

require __DIR__ . '/partials/header.php';
?>
<section class="content-grid two-col editorial-grid">
  <article class="card feature-card">
    <span class="eyebrow">Our position</span>
    <h2>Design-forward outside. Operationally useful inside.</h2>
    <p>Black Hole AI Pro is built to be more than a simple chatbot demo. The goal is a complete website experience that brings brand, product, content, and workspace together in one coherent system.</p>
  </article>
  <article class="card stat-card premium-card">
    <h3>Why it matters</h3>
    <ul class="feature-list">
      <li>Visitors get a real homepage instead of landing directly on a raw tool</li>
      <li>Teams can add more products and pages without a redesign</li>
      <li>Secure auth and storage keep the system practical for ongoing use</li>
    </ul>
  </article>
</section>
<section class="content-grid three-col">
  <article class="card mini-card">
    <span class="eyebrow">Architecture</span>
    <h3>PHP, SQLite, and modular structure</h3>
    <p>The project is organized into reusable services, helpers, content definitions, and public pages.</p>
  </article>
  <article class="card mini-card">
    <span class="eyebrow">Experience</span>
    <h3>Dark luxury presentation</h3>
    <p>Branding leans into black, gold, motion, and strong framing instead of plain boilerplate panels.</p>
  </article>
  <article class="card mini-card">
    <span class="eyebrow">Growth</span>
    <h3>Ready for future modules</h3>
    <p>Admin dashboards, analytics, saved chats, and service pages can grow from this base cleanly.</p>
  </article>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
