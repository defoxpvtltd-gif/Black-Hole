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
$name = '';
$email = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
        $errors[] = 'Invalid form token. Please refresh and try again.';
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['password_confirmation'] ?? '');

    if ($name === '' || mb_strlen($name) < 2) {
        $errors[] = 'Name must be at least 2 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (mb_strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Password confirmation does not match.';
    }

    if ($errors === []) {
        $repo = new App\Auth\UserRepository();
        if ($repo->findByEmail($email)) {
            $errors[] = 'This email is already registered.';
        } else {
            $user = $auth->register($name, $email, $password);
            $auth->loginUser($user);
            Flash::set('success', 'Account created successfully. Welcome to Black Hole AI Pro.');
            App\Http\Redirect::to(App\Support\Url::to('/chat.php'));
        }
    }
}

$title = $appName . ' � Sign Up';
$pageTitle = 'Create your account';
$pageSubtitle = 'Start with a secure account, then move into the AI workspace.';
$user = null;
require __DIR__ . '/partials/header.php';
?>
<section class="content-grid form-centered">
  <article class="card auth-card">
    <h2>Sign up</h2>
    <p class="muted-text">Create your secure workspace account.</p>
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
        <span>Name</span>
        <input type="text" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required />
      </label>
      <label>
        <span>Email</span>
        <input type="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required />
      </label>
      <label>
        <span>Password</span>
        <input type="password" name="password" required />
      </label>
      <label>
        <span>Confirm Password</span>
        <input type="password" name="password_confirmation" required />
      </label>
      <button class="button primary" type="submit">Create Account</button>
    </form>
  </article>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
