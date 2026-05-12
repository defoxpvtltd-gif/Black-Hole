<?php
declare(strict_types=1);

namespace App\Support;

final class Flash
{
    private const SESSION_KEY = '__flash';

    public static function set(string $type, string $message): void
    {
        $_SESSION[self::SESSION_KEY] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public static function get(): ?array
    {
        $flash = $_SESSION[self::SESSION_KEY] ?? null;
        unset($_SESSION[self::SESSION_KEY]);
        return is_array($flash) ? $flash : null;
    }
}
