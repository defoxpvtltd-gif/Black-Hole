<?php
declare(strict_types=1);

use App\Auth\AuthService;
use App\Config\Env;
use App\Security\Csrf;
use App\Support\Flash;

require dirname(__DIR__) . '/bootstrap/app.php';

$auth = new AuthService();
$auth->requireGuest();
$appName = (string) Env::get('APP_NAME', 'Black Hole AI Pro');
$flash = Flash::get();
$errors = [];
$email = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
        $errors[] = 'Invalid form token. Please refresh and try again.';
    }

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if ($errors === [] && !$auth->attempt($email, $password)) {
        $errors[] = 'Invalid email or password.';
    }

    if ($errors === []) {
        Flash::set('success', 'Login successful. Welcome back.');
        App\Http\Redirect::to(App\Support\Url::to('/chat.php'));
    }
}

$title = $appName . ' � Login';
$pageTitle = 'Login to your workspace';
$pageSubtitle = 'Continue to the protected Black Hole AI chat console.';
$user = null;
require __DIR__ . '/partials/header.php';
?>
<section class="content-grid form-centered">
  <article class="card auth-card">
    <h2>Login</h2>
    <p class="muted-text">Access your AI workspace securely.</p>
    <?php if ($errors !== []): ?>
      <div class="form-errors">
        <?php foreach ($errors as $error): ?>
          <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <form method="post" class="stack-form">
      <input type="hidden" name="_token" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>" />
      <label>
        <span>Email</span>
        <input type="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required />
      </label>
      <label>
        <span>Password</span>
        <input type="password" name="password" required />
      </label>
      <button class="button primary" type="submit">Login</button>
    </form>
  </article>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
