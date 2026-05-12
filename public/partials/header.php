<?php
declare(strict_types=1);

use App\Content\SiteCatalog;
use App\Support\Url;

$title = $title ?? 'Black Hole AI Pro';
$pageTitle = $pageTitle ?? $title;
$pageSubtitle = $pageSubtitle ?? 'Professional AI workspace';
$appName = $appName ?? 'Black Hole AI Pro';
$user = $user ?? null;
$flash = $flash ?? null;
$currentPath = basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'));
$pageCount = $pageCount ?? SiteCatalog::count();
$categoryCount = $categoryCount ?? count(SiteCatalog::categories());
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= htmlspecialchars(Url::to('/assets/css/app.css'), ENT_QUOTES, 'UTF-8') ?>" />
</head>
<body>
  <div class="bg-orbit"></div>
  <div class="bg-noise"></div>
  <main class="site-shell">
    <header class="site-header">
      <div class="header-brand-block">
        <a class="brand" href="<?= htmlspecialchars(Url::to('/index.php'), ENT_QUOTES, 'UTF-8') ?>">
          <span class="brand-dot" aria-hidden="true"></span>
          <span>
            <strong><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></strong>
            <small>Curated AI product suite</small>
          </span>
        </a>
        <a class="brand-social-link" href="https://www.instagram.com/ayat_rahman7690/" target="_blank" rel="noopener noreferrer">@ayat_rahman7690</a>
        <div class="header-meta">
          <span class="meta-pill"><?= htmlspecialchars((string) $pageCount, ENT_QUOTES, 'UTF-8') ?> curated pages</span>
          <span class="meta-pill"><?= htmlspecialchars((string) $categoryCount, ENT_QUOTES, 'UTF-8') ?> core sections</span>
          <span class="meta-pill">Black Hole V1.3</span>
        </div>
      </div>

      <div class="header-nav-block">
        <nav class="site-nav">
          <a class="nav-link<?= $currentPath === 'index.php' ? ' active' : '' ?>" href="<?= htmlspecialchars(Url::to('/index.php'), ENT_QUOTES, 'UTF-8') ?>">Home</a>
          <a class="nav-link<?= $currentPath === 'pages.php' ? ' active' : '' ?>" href="<?= htmlspecialchars(Url::to('/pages.php'), ENT_QUOTES, 'UTF-8') ?>">Library</a>
          <a class="nav-link<?= $currentPath === 'solutions.php' ? ' active' : '' ?>" href="<?= htmlspecialchars(Url::to('/solutions.php'), ENT_QUOTES, 'UTF-8') ?>">Solutions</a>
          <a class="nav-link<?= $currentPath === 'resources.php' ? ' active' : '' ?>" href="<?= htmlspecialchars(Url::to('/resources.php'), ENT_QUOTES, 'UTF-8') ?>">Resources</a>
          <a class="nav-link<?= $currentPath === 'pricing.php' ? ' active' : '' ?>" href="<?= htmlspecialchars(Url::to('/pricing.php'), ENT_QUOTES, 'UTF-8') ?>">Pricing</a>
          <a class="nav-link<?= $currentPath === 'about.php' ? ' active' : '' ?>" href="<?= htmlspecialchars(Url::to('/about.php'), ENT_QUOTES, 'UTF-8') ?>">About</a>
          <a class="nav-link<?= $currentPath === 'contact.php' ? ' active' : '' ?>" href="<?= htmlspecialchars(Url::to('/contact.php'), ENT_QUOTES, 'UTF-8') ?>">Contact</a>
        </nav>

        <div class="nav-actions">
          <?php if (is_array($user)): ?>
            <a class="button primary<?= $currentPath === 'chat.php' ? ' is-current' : '' ?>" href="<?= htmlspecialchars(Url::to('/chat.php'), ENT_QUOTES, 'UTF-8') ?>">Open Chat</a>
            <a class="button subtle-button" href="<?= htmlspecialchars(Url::to('/logout.php'), ENT_QUOTES, 'UTF-8') ?>">Logout</a>
          <?php else: ?>
            <a class="button subtle-button<?= $currentPath === 'login.php' ? ' is-current' : '' ?>" href="<?= htmlspecialchars(Url::to('/login.php'), ENT_QUOTES, 'UTF-8') ?>">Login</a>
            <a class="button primary<?= $currentPath === 'signup.php' ? ' is-current' : '' ?>" href="<?= htmlspecialchars(Url::to('/signup.php'), ENT_QUOTES, 'UTF-8') ?>">Start Free</a>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <?php if (is_array($flash) && isset($flash['message'])): ?>
      <div class="flash flash-<?= htmlspecialchars((string) ($flash['type'] ?? 'info'), ENT_QUOTES, 'UTF-8') ?>">
        <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <section class="page-hero">
      <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
      <p><?= htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8') ?></p>
    </section>
