<?php
declare(strict_types=1);

use App\Auth\AuthService;
use App\Support\Flash;
use App\Support\Url;

require dirname(__DIR__) . '/bootstrap/app.php';

$auth = new AuthService();
$auth->logout();
Flash::set('success', 'You have been logged out successfully.');
App\Http\Redirect::to(Url::to('/index.php'));
