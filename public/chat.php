<?php
declare(strict_types=1);

use App\Auth\AuthService;
use App\Config\Env;
use App\Security\Csrf;
use App\Support\Flash;
use App\Support\Url;

require dirname(__DIR__) . '/bootstrap/app.php';

$auth = new AuthService();
$auth->requireAuth();
$user = $auth->user();
$appName = (string) Env::get('APP_NAME', 'Black Hole AI Pro');
$flash = Flash::get();
$title = $appName . ' - Chat';
$modelName = (string) Env::get('OPENROUTER_MODEL', 'openrouter/auto');
$displayModelName = (string) Env::get('APP_MODEL_LABEL', 'Black Hole V1.3');

$history = [];
foreach ((array) ($_SESSION['chat_history'] ?? []) as $item) {
    if (!is_array($item)) {
        continue;
    }

    $role = (string) ($item['role'] ?? '');
    $content = trim((string) ($item['content'] ?? ''));
    if ($content === '' || ($role !== 'user' && $role !== 'assistant')) {
        continue;
    }

    $history[] = [
        'role' => $role,
        'content' => $content,
    ];
}

$suggestedPrompts = [
    'Create a clean project launch checklist for my website.',
    'Write a polished client proposal for my AI service.',
    'Explain this PHP architecture in simple language.',
    'Who made you',
];
$defaultAssistantMessage = 'Black Hole created by Ayat Rahman.';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= htmlspecialchars(Url::to('/assets/css/app.css'), ENT_QUOTES, 'UTF-8') ?>" />
</head>
<body class="chat-app-body">
  <div class="bg-orbit"></div>
  <div class="bg-noise"></div>

  <div class="chat-app-shell">
    <button id="sidebarBackdrop" class="chat-sidebar-backdrop" type="button" aria-label="Close sidebar"></button>

    <aside id="chatSidebar" class="chat-sidebar">
      <div class="chat-sidebar-head">
        <a class="chat-brand" href="<?= htmlspecialchars(Url::to('/index.php'), ENT_QUOTES, 'UTF-8') ?>">
          <span class="chat-brand-mark" aria-hidden="true"></span>
          <span>
            <strong><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></strong>
            <small>Black and gold workspace</small>
          </span>
        </a>
        <a class="brand-social-link chat-brand-social-link" href="https://www.instagram.com/ayat_rahman7690/" target="_blank" rel="noopener noreferrer">@ayat_rahman7690</a>
        <button id="sidebarClose" class="chat-icon-button mobile-only" type="button">Close</button>
      </div>

      <button id="newChat" class="chat-primary-action" type="button">New Chat</button>

      <section class="sidebar-section">
        <p class="sidebar-label">Navigate</p>
        <div class="sidebar-nav-list">
          <a class="sidebar-link" href="<?= htmlspecialchars(Url::to('/index.php'), ENT_QUOTES, 'UTF-8') ?>">Home</a>
          <a class="sidebar-link" href="<?= htmlspecialchars(Url::to('/pages.php'), ENT_QUOTES, 'UTF-8') ?>">Library</a>
          <a class="sidebar-link" href="<?= htmlspecialchars(Url::to('/solutions.php'), ENT_QUOTES, 'UTF-8') ?>">Solutions</a>
          <a class="sidebar-link" href="<?= htmlspecialchars(Url::to('/pricing.php'), ENT_QUOTES, 'UTF-8') ?>">Pricing</a>
          <a class="sidebar-link" href="<?= htmlspecialchars(Url::to('/contact.php'), ENT_QUOTES, 'UTF-8') ?>">Contact</a>
        </div>
      </section>

      <section class="sidebar-section">
        <p class="sidebar-label">Quick prompts</p>
        <div class="sidebar-prompt-list">
          <?php foreach ($suggestedPrompts as $prompt): ?>
            <button class="sidebar-prompt" type="button" data-prompt="<?= htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8') ?></button>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="sidebar-section grow">
        <div class="sidebar-section-bar">
          <p class="sidebar-label">Recent in this session</p>
          <button id="clearChat" class="sidebar-clear" type="button">Clear</button>
        </div>
        <div id="recentQuestions" class="sidebar-recent"></div>
      </section>

      <div class="chat-sidebar-foot">
        <span class="meta-pill"><?= htmlspecialchars($displayModelName, ENT_QUOTES, 'UTF-8') ?></span>
        <span class="meta-pill">Private session</span>
      </div>
    </aside>

    <section class="chat-main">
      <header class="chat-main-topbar">
        <div class="chat-topbar-left">
          <button id="sidebarToggle" class="chat-icon-button mobile-only" type="button">Menu</button>
          <div>
            <h1>Black Hole V1.3</h1>
          </div>
        </div>
        <div class="chat-topbar-right">
          <span id="activeModelLabel" class="topbar-badge"><?= htmlspecialchars($displayModelName, ENT_QUOTES, 'UTF-8') ?></span>
          <span class="topbar-badge">Private workspace</span>
        </div>
      </header>

      <?php if (is_array($flash) && isset($flash['message'])): ?>
        <div class="chat-inline-flash chat-inline-flash-<?= htmlspecialchars((string) ($flash['type'] ?? 'info'), ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <section id="chatStage" class="chat-stage">
        <section id="emptyState" class="chat-empty-state">
          <span class="eyebrow">Black Hole V1.3</span>
          <h2>How can I help you build today?</h2>
          <p>This workspace keeps your black and gold style while giving you a cleaner chat experience and a more focused assistant surface.</p>
          <div class="chat-empty-grid">
            <?php foreach ($suggestedPrompts as $prompt): ?>
              <button class="chat-prompt-card" type="button" data-prompt="<?= htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8') ?>">
                <strong><?= htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8') ?></strong>
                <span>Tap to send this prompt</span>
              </button>
            <?php endforeach; ?>
          </div>
        </section>

        <section id="messages" class="chat-thread" aria-live="polite"></section>
      </section>

      <footer class="chat-composer-shell">
        <form id="chatForm" class="chat-composer-form">
          <label class="sr-only" for="messageInput">Message</label>
          <textarea id="messageInput" rows="1" autocomplete="off" placeholder="Message Black Hole..." spellcheck="true"></textarea>
          <div class="chat-composer-bar">
            <div class="chat-composer-tools">
              <span class="composer-hint">Enter to send</span>
              <span class="composer-hint">Shift + Enter for new line</span>
            </div>
            <button id="sendButton" class="chat-send-button" type="submit">Send</button>
          </div>
        </form>
        <p class="chat-disclaimer">Black Hole can make mistakes. Please verify important details.</p>
        <p class="chat-credit">Designed and developed by Ayat Rahman. <a href="https://www.instagram.com/ayat_rahman7690/" target="_blank" rel="noopener noreferrer">@ayat_rahman7690</a></p>
      </footer>
    </section>
  </div>

  <script>
  window.APP_CONFIG = {
    basePath: <?= json_encode(Url::basePath(), JSON_UNESCAPED_SLASHES) ?>,
    csrfToken: <?= json_encode(Csrf::token(), JSON_UNESCAPED_SLASHES) ?>,
    chatEndpoint: <?= json_encode(Url::to('/api/chat.php'), JSON_UNESCAPED_SLASHES) ?>,
    clearEndpoint: <?= json_encode(Url::to('/api/clear.php'), JSON_UNESCAPED_SLASHES) ?>,
    loginUrl: <?= json_encode(Url::to('/login.php'), JSON_UNESCAPED_SLASHES) ?>,
    userName: <?= json_encode((string) ($user['name'] ?? 'User'), JSON_UNESCAPED_SLASHES) ?>,
    modelName: <?= json_encode($modelName, JSON_UNESCAPED_SLASHES) ?>,
    modelLabel: <?= json_encode($displayModelName, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
    defaultAssistantMessage: <?= json_encode($defaultAssistantMessage, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
    initialHistory: <?= json_encode($history, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
    suggestedPrompts: <?= json_encode($suggestedPrompts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
  };
  </script>
  <script src="<?= htmlspecialchars(Url::to('/assets/js/app.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
