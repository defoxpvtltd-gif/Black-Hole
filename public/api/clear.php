<?php
declare(strict_types=1);

use App\Auth\AuthService;
use App\Http\JsonResponse;
use App\Security\Csrf;

require dirname(__DIR__, 2) . '/bootstrap/app.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    JsonResponse::send(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$auth = new AuthService();
if (!$auth->check()) {
    JsonResponse::send(['ok' => false, 'error' => 'Unauthorized. Please login first.'], 401);
}

$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
if (!Csrf::validate(is_string($csrfToken) ? $csrfToken : null)) {
    JsonResponse::send(['ok' => false, 'error' => 'Invalid CSRF token'], 419);
}

$_SESSION['chat_history'] = [];

JsonResponse::send([
    'ok' => true,
    'message' => 'Conversation cleared.',
    'meta' => ['timestamp' => date('c')],
]);
