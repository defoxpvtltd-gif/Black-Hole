<?php
declare(strict_types=1);

namespace App\Security;

final class Csrf
{
    private const SESSION_KEY = '__csrf_token';

    public static function ensureToken(): void
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
    }

    public static function token(): string
    {
        self::ensureToken();
        return (string) $_SESSION[self::SESSION_KEY];
    }

    public static function validate(?string $submitted): bool
    {
        self::ensureToken();
        if ($submitted === null || $submitted === '') {
            return false;
        }

        $sessionToken = (string) $_SESSION[self::SESSION_KEY];
        return hash_equals($sessionToken, $submitted);
    }
}
