<?php
declare(strict_types=1);

namespace App\Support;

final class Logger
{
    public static function error(string $message): void
    {
        self::write('ERROR', $message);
    }

    public static function info(string $message): void
    {
        self::write('INFO', $message);
    }

    private static function write(string $level, string $message): void
    {
        $logDir = APP_BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }

        $line = sprintf("[%s] %s %s\n", date('c'), $level, $message);
        @file_put_contents($logDir . '/app.log', $line, FILE_APPEND);
    }
}
