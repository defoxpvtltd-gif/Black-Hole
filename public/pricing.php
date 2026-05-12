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
$title = $appName . ' - Pricing';
$pageTitle = 'Pricing designed for a premium AI product story';
$pageSubtitle = 'Use these plans as a polished starting point for demos, proposals, or real product packaging.';

require __DIR__ . '/partials/header.php';
?>
<section class="content-grid three-col pricing-grid">
  <article class="card price-card">
    <span class="eyebrow">Starter</span>
    <h2>$49<span>/mo</span></h2>
    <p>For solo operators who need a branded AI web presence and simple workspace access.</p>
    <ul class="feature-list tight-list">
      <li>Core website pages</li>
      <li>Secure signup and login</li>
      <li>Protected AI chat access</li>
    </ul>
    <a class="button primary" href="<?= htmlspecialchars(Url::to('/signup.php'), ENT_QUOTES, 'UTF-8') ?>">Start now</a>
  </article>
  <article class="card price-card featured-price">
    <span class="eyebrow">Growth</span>
    <h2>$149<span>/mo</span></h2>
    <p>For teams that want richer content, stronger positioning, and a cleaner AI product story.</p>
    <ul class="feature-list tight-list">
      <li>All website sections</li>
      <li>Multi-page content architecture</li>
      <li>Priority setup guidance</li>
    </ul>
    <a class="button primary" href="<?= htmlspecialchars(Url::to('/contact.php'), ENT_QUOTES, 'UTF-8') ?>">Talk to sales</a>
  </article>
  <article class="card price-card">
    <span class="eyebrow">Enterprise</span>
    <h2>Custom</h2>
    <p>For organizations that need tailored operations, extra modules, and a more private deployment path.</p>
    <ul class="feature-list tight-list">
      <li>Custom architecture planning</li>
      <li>Advanced workflow layering</li>
      <li>Security and governance review</li>
    </ul>
    <a class="button primary" href="<?= htmlspecialchars(Url::to('/contact.php'), ENT_QUOTES, 'UTF-8') ?>">Book a call</a>
  </article>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
