<?php
declare(strict_types=1);

namespace App\AI;

use App\Config\Env;

final class ChatService
{
    private const CREATOR_RESPONSE = 'Ayat Rahman is my creator.';
    private const GREETING_RESPONSE = 'Hello! How can I help you today?';
    private const EMPTY_FALLBACK = 'I could not generate a clean model response just now. Please try again or rephrase your message.';

    private const CREATOR_PATTERNS = [
        'who made you',
        'who make you',
        'who created you',
        'who create you',
        'who built you',
        'who is your creator',
        'kisne banaya',
        'kis ne banaya',
        'tumhe kisne banaya',
        'tumhe kis ne banaya',
        'aapko kisne banaya',
    ];

    private const GREETING_PATTERNS = [
        'hi',
        'hello',
        'hey',
        'salam',
        'assalamualaikum',
        'aoa',
    ];

    private const SYSTEM_PROMPT = 'You are Black Hole, a professional AI assistant. '
        . 'Be clear, concise, helpful, and practical. '
        . 'If the user asks who created you, reply exactly: "Ayat Rahman is my creator."';

    private OpenRouterClient $client;
    private string $model;
    private array $fallbackModels;
    private int $maxHistory;

    public function __construct(OpenRouterClient $client)
    {
        $this->client = $client;
        $this->model = (string) Env::get('OPENROUTER_MODEL', 'openrouter/auto');
        $fallbackString = (string) Env::get('OPENROUTER_FALLBACK_MODELS', 'openrouter/free');
        $this->fallbackModels = $this->parseFallbacks($fallbackString);
        $this->maxHistory = max(4, Env::getInt('CHAT_MAX_HISTORY', 24));
    }

    public function reply(string $input, array $history): array
    {
        if ($this->isCreatorQuestion($input)) {
            return [
                'ok' => true,
                'text' => self::CREATOR_RESPONSE,
                'model' => 'rule-based',
            ];
        }

        if ($this->isGreeting($input)) {
            return [
                'ok' => true,
                'text' => self::GREETING_RESPONSE,
                'model' => 'local-greeting',
            ];
        }

        $messages = [['role' => 'system', 'content' => self::SYSTEM_PROMPT]];
        foreach ($this->trimHistory($history) as $item) {
            if (!isset($item['role'], $item['content'])) {
                continue;
            }

            $role = (string) $item['role'];
            $content = trim((string) $item['content']);
            if ($content === '' || ($role !== 'assistant' && $role !== 'user')) {
                continue;
            }

            $messages[] = ['role' => $role, 'content' => $content];
        }
        $messages[] = ['role' => 'user', 'content' => $input];

        $result = $this->client->chat($messages, $this->model, $this->fallbackModels);
        if (($result['ok'] ?? false) === true) {
            return $result;
        }

        $error = strtolower((string) ($result['error'] ?? ''));
        if (str_contains($error, 'empty response from model')) {
            return [
                'ok' => true,
                'text' => self::EMPTY_FALLBACK,
                'model' => 'fallback-message',
            ];
        }

        return $result;
    }

    public function trimHistory(array $history): array
    {
        if (count($history) <= $this->maxHistory) {
            return $history;
        }

        return array_slice($history, -1 * $this->maxHistory);
    }

    private function isGreeting(string $text): bool
    {
        $normalized = strtolower(trim($text));
        $normalized = preg_replace('/[^a-z0-9\s]/', ' ', $normalized) ?? '';
        $normalized = trim(preg_replace('/\s+/', ' ', $normalized) ?? '');

        return in_array($normalized, self::GREETING_PATTERNS, true);
    }

    private function isCreatorQuestion(string $text): bool
    {
        $normalized = strtolower($text);
        $normalized = preg_replace('/[^a-z0-9\s]/', ' ', $normalized) ?? '';
        $normalized = trim(preg_replace('/\s+/', ' ', $normalized) ?? '');

        foreach (self::CREATOR_PATTERNS as $pattern) {
            if (str_contains($normalized, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function parseFallbacks(string $value): array
    {
        $parts = array_map(static fn ($item): string => trim($item), explode(',', $value));
        $parts = array_values(array_filter($parts, static fn ($item): bool => $item !== ''));

        if ($parts === []) {
            return ['openrouter/free'];
        }

        return $parts;
    }
}