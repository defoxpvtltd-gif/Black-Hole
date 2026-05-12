<?php
declare(strict_types=1);

namespace App\Support;

final class Url
{
    public static function basePath(): string
    {
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $directory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($directory === '.' || $directory === '') {
            return '';
        }

        return $directory;
    }

    public static function to(string $path = ''): string
    {
        $base = self::basePath();
        if ($path === '') {
            return $base === '' ? '/' : $base . '/';
        }

        $normalized = '/' . ltrim($path, '/');
        return ($base === '' ? '' : $base) . $normalized;
    }
}
