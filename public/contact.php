<?php
declare(strict_types=1);

use App\Auth\AuthService;
use App\Config\Env;
use App\Database\Database;
use App\Http\Redirect;
use App\Security\Csrf;
use App\Support\Flash;
use App\Support\Url;

require dirname(__DIR__) . '/bootstrap/app.php';

$auth = new AuthService();
$user = $auth->user();
$appName = (string) Env::get('APP_NAME', 'Black Hole AI Pro');
$flash = Flash::get();
$errors = [];
$old = [
    'name' => '',
    'email' => '',
    'company' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name'] = trim((string) ($_POST['name'] ?? ''));
    $old['email'] = trim((string) ($_POST['email'] ?? ''));
    $old['company'] = trim((string) ($_POST['company'] ?? ''));
    $old['message'] = trim((string) ($_POST['message'] ?? ''));
    $token = (string) ($_POST['_csrf'] ?? '');

    if (!Csrf::validate($token)) {
        $errors[] = 'Security validation failed. Please refresh the page and try again.';
    }
    if ($old['name'] === '') {
        $errors[] = 'Name is required.';
    }
    if ($old['email'] === '' || filter_var($old['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'A valid email is required.';
    }
    if ($old['message'] === '') {
        $errors[] = 'Message is required.';
    }

    if ($errors === []) {
        $pdo = Database::connection();
        $statement = $pdo->prepare('INSERT INTO contact_messages (name, email, company, message, created_at) VALUES (:name, :email, :company, :message, :created_at)');
        $statement->execute([
            ':name' => $old['name'],
            ':email' => $old['email'],
            ':company' => $old['company'],
            ':message' => $old['message'],
            ':created_at' => date('c'),
        ]);

        Flash::set('success', 'Your message has been sent. We can continue building from here later.');
        Redirect::to(Url::to('/contact.php'));
    }
}

$title = $appName . ' - Contact';
$pageTitle = 'Contact the Black Hole AI team';
$pageSubtitle = 'Use this page for project setup, pricing discussions, custom build requests, or rollout planning.';

require __DIR__ . '/partials/header.php';
?>
<section class="content-grid two-col contact-layout">
  <article class="card feature-card">
    <span class="eyebrow">Contact desk</span>
    <h2>Send project details and keep the website moving forward</h2>
    <p>This form stores messages in the local project database, so the site now has a real business intake point instead of only static pages.</p>
    <ul class="feature-list">
      <li>Custom website expansion requests</li>
      <li>Admin panel, analytics, or workflow modules</li>
      <li>Branding, pricing, and deployment setup conversations</li>
    </ul>
  </article>
  <article class="card auth-card">
    <h2>Project inquiry</h2>
    <?php if ($errors !== []): ?>
      <div class="form-errors">
        <?php foreach ($errors as $error): ?>
          <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <form class="stack-form" method="post">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
      <label>
        <span>Name</span>
        <input type="text" name="name" value="<?= htmlspecialchars($old['name'], ENT_QUOTES, 'UTF-8') ?>" />
      </label>
      <label>
        <span>Email</span>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8') ?>" />
      </label>
      <label>
        <span>Company</span>
        <input type="text" name="company" value="<?= htmlspecialchars($old['company'], ENT_QUOTES, 'UTF-8') ?>" />
      </label>
      <label>
        <span>Message</span>
        <textarea name="message" rows="6"><?= htmlspecialchars($old['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
      </label>
      <button type="submit" class="button primary">Send message</button>
    </form>
  </article>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
