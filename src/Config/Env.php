<?php
declare(strict_types=1);

namespace App\Config;

final class Env
{
    private static array $values = [];

    public static function load(string $path, bool $override = false): void
    {
        if (!is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            $parts = explode('=', $trimmed, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            if ($key === '') {
                continue;
            }

            if (!$override && array_key_exists($key, self::$values)) {
                continue;
            }

            $value = self::normalizeValue(trim($parts[1]));
            self::$values[$key] = $value;
            if ($override || getenv($key) === false) {
                putenv($key . '=' . $value);
            }
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, self::$values)) {
            return self::$values[$key];
        }

        $server = $_SERVER[$key] ?? null;
        if (is_string($server)) {
            self::$values[$key] = $server;
            return $server;
        }

        $env = getenv($key);
        if ($env !== false) {
            $value = (string) $env;
            self::$values[$key] = $value;
            return $value;
        }

        return $default;
    }

    public static function getInt(string $key, int $default): int
    {
        $value = self::get($key);
        if ($value === null || $value === '') {
            return $default;
        }

        if (!is_numeric($value)) {
            return $default;
        }

        return (int) $value;
    }

    public static function getBool(string $key, bool $default): bool
    {
        $value = strtolower(trim((string) self::get($key, '')));
        if ($value === '') {
            return $default;
        }

        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    private static function normalizeValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $first = $value[0];
        $last = $value[strlen($value) - 1];
        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
