<?php
declare(strict_types=1);

namespace App\AI;

use App\Config\Env;

final class OpenRouterClient
{
    private string $apiKey;
    private string $endpoint;
    private string $appUrl;
    private string $appName;
    private int $timeoutSeconds;
    private int $connectTimeoutSeconds;
    private int $retryCount;
    private int $retryDelayMs;
    private bool $forceIpv4;
    private bool $verifySsl;
    private ?string $caInfo;

    public function __construct(string $apiKey)
    {
        $this->apiKey = trim($apiKey);
        $this->endpoint = 'https://openrouter.ai/api/v1/chat/completions';
        $this->appUrl = (string) Env::get('OPENROUTER_APP_URL', (string) Env::get('APP_URL', 'http://localhost'));
        $this->appName = (string) Env::get('OPENROUTER_APP_NAME', (string) Env::get('APP_NAME', 'Black Hole AI Pro'));
        $this->timeoutSeconds = max(15, Env::getInt('REQUEST_TIMEOUT_SECONDS', 60));
        $this->connectTimeoutSeconds = max(5, Env::getInt('OPENROUTER_CONNECT_TIMEOUT_SECONDS', 20));
        $this->retryCount = max(0, Env::getInt('OPENROUTER_RETRY_COUNT', 1));
        $this->retryDelayMs = max(0, Env::getInt('OPENROUTER_RETRY_DELAY_MS', 1200));
        $this->forceIpv4 = Env::getBool('OPENROUTER_FORCE_IPV4', true);
        $this->verifySsl = Env::getBool('OPENROUTER_SSL_VERIFY', true);
        $caInfo = trim((string) Env::get('OPENROUTER_CAINFO', ''));
        $this->caInfo = $caInfo !== '' ? $caInfo : null;
    }

    public function chat(
        array $messages,
        string $model,
        array $fallbackModels = [],
        float $temperature = 0.7
    ): array {
        if (!function_exists('curl_init')) {
            return [
                'ok' => false,
                'status' => 500,
                'error' => 'PHP cURL extension is not enabled.',
            ];
        }

        if ($this->apiKey === '' || str_contains(strtolower($this->apiKey), 'your_openrouter_api_key_here')) {
            return [
                'ok' => false,
                'status' => 500,
                'error' => 'OPENROUTER_API_KEY is missing or still set to the placeholder value.',
            ];
        }

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
        ];

        $fallbacks = array_values(
            array_filter(
                array_map(static fn ($value): string => trim((string) $value), $fallbackModels),
                static fn (string $value): bool => $value !== ''
            )
        );

        if ($fallbacks !== []) {
            $payload['models'] = array_values(array_unique(array_merge([$model], $fallbacks)));
            $payload['route'] = 'fallback';
        }

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: Black-Hole-AI-Pro/1.0',
            'HTTP-Referer: ' . $this->appUrl,
            'X-Title: ' . $this->appName,
            'Expect:',
        ];

        $attempt = 0;
        do {
            $attempt++;
            $result = $this->performRequest($payload, $headers);

            if (($result['ok'] ?? false) === true) {
                return $result;
            }

            $errno = (int) ($result['curl_errno'] ?? 0);
            $shouldRetry = in_array($errno, [6, 7, 28, 35], true) && $attempt <= $this->retryCount;
            if ($shouldRetry && $this->retryDelayMs > 0) {
                usleep($this->retryDelayMs * 1000);
            }
        } while ($shouldRetry);

        return $result;
    }

    private function performRequest(array $payload, array $headers): array
    {
        $ch = curl_init($this->endpoint);
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeoutSeconds,
            CURLOPT_FAILONERROR => false,
            CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
            CURLOPT_SSL_VERIFYHOST => $this->verifySsl ? 2 : 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ];

        if ($this->forceIpv4 && defined('CURL_IPRESOLVE_V4')) {
            $options[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
        }

        if ($this->caInfo !== null) {
            $options[CURLOPT_CAINFO] = $this->caInfo;
        }

        curl_setopt_array($ch, $options);

        $raw = curl_exec($ch);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            return [
                'ok' => false,
                'status' => 0,
                'curl_errno' => $curlErrno,
                'error' => $this->mapCurlError($curlErrno, $curlError),
            ];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [
                'ok' => false,
                'status' => $status,
                'error' => 'Invalid API response.',
            ];
        }

        if ($status >= 400) {
            $errorMessage = (string) ($decoded['error']['message'] ?? ('HTTP ' . $status));
            return [
                'ok' => false,
                'status' => $status,
                'error' => $errorMessage,
            ];
        }

        $content = $decoded['choices'][0]['message']['content'] ?? null;
        if (is_array($content)) {
            $parts = [];
            foreach ($content as $segment) {
                if (is_array($segment) && isset($segment['text']) && is_string($segment['text'])) {
                    $parts[] = $segment['text'];
                }
            }
            $content = implode('', $parts);
        }

        $text = is_string($content) ? trim($content) : '';
        if ($text === '') {
            return [
                'ok' => false,
                'status' => $status,
                'error' => 'Empty response from model.',
            ];
        }

        return [
            'ok' => true,
            'status' => $status,
            'text' => $text,
            'model' => (string) ($decoded['model'] ?? $payload['model']),
            'usage' => $decoded['usage'] ?? null,
        ];
    }

    private function mapCurlError(int $errno, string $curlError): string
    {
        if ($errno === 28) {
            return 'Network timeout while connecting to OpenRouter. Try OPENROUTER_FORCE_IPV4=true and check firewall/antivirus/ISP SSL blocking.';
        }

        if ($errno === 35) {
            return 'SSL connection failed. Check XAMPP/PHP OpenSSL setup or try setting OPENROUTER_CAINFO to your cacert.pem path.';
        }

        if ($errno === 6 || $errno === 7) {
            return 'OpenRouter host connection failed. Check internet, DNS, or firewall settings.';
        }

        return 'Network error: ' . $curlError;
    }
}
