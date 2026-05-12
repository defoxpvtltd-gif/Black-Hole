<?php
declare(strict_types=1);

use App\Auth\AuthService;
use App\AI\ChatService;
use App\AI\OpenRouterClient;
use App\Config\Env;
use App\Http\JsonResponse;
use App\Security\Csrf;
use App\Security\RateLimiter;
use App\Support\Logger;

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

$limitPerMinute = max(5, Env::getInt('RATE_LIMIT_PER_MINUTE', 30));
if (!RateLimiter::allow('chat', $limitPerMinute, 60)) {
    JsonResponse::send(['ok' => false, 'error' => 'Too many requests. Please wait a moment.'], 429);
}

$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody ?: '', true);
if (!is_array($payload)) {
    JsonResponse::send(['ok' => false, 'error' => 'Invalid JSON body'], 400);
}

$message = trim((string) ($payload['message'] ?? ''));
if ($message === '') {
    JsonResponse::send(['ok' => false, 'error' => 'Message is required'], 422);
}

if (mb_strlen($message) > 4000) {
    JsonResponse::send(['ok' => false, 'error' => 'Message too long (max 4000 characters)'], 422);
}

$apiKey = trim((string) Env::get('OPENROUTER_API_KEY', ''));
if ($apiKey === '') {
    JsonResponse::send(['ok' => false, 'error' => 'Missing OPENROUTER_API_KEY in environment'], 500);
}

$history = $_SESSION['chat_history'] ?? [];
if (!is_array($history)) {
    $history = [];
}

$service = new ChatService(new OpenRouterClient($apiKey));
$result = $service->reply($message, $history);

if (!($result['ok'] ?? false)) {
    $status = (int) ($result['status'] ?? 500);
    $error = (string) ($result['error'] ?? 'Unknown AI error');
    Logger::error('OpenRouter error: ' . $error);

    $response = ['ok' => false, 'error' => $error];
    if ($status === 0 || $status >= 500) {
        $curlVersion = function_exists('curl_version') ? curl_version() : null;
        $response['diagnostic'] = [
            'php' => PHP_VERSION,
            'curl' => is_array($curlVersion) ? ($curlVersion['version'] ?? 'unknown') : 'missing',
            'ssl' => is_array($curlVersion) ? ($curlVersion['ssl_version'] ?? 'unknown') : 'missing',
        ];
    }

    JsonResponse::send($response, $status >= 400 ? $status : 500);
}

$replyText = (string) ($result['text'] ?? '');
$history[] = ['role' => 'user', 'content' => $message];
$history[] = ['role' => 'assistant', 'content' => $replyText];
$history = $service->trimHistory($history);
$_SESSION['chat_history'] = $history;

JsonResponse::send([
    'ok' => true,
    'reply' => $replyText,
    'meta' => [
        'model' => (string) ($result['model'] ?? ''),
        'timestamp' => date('c'),
    ],
]);
