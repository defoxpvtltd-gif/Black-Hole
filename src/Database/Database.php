<?php
declare(strict_types=1);

namespace App\Database;

use PDO;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $dbDir = APP_BASE_PATH . '/storage/database';
        if (!is_dir($dbDir) && !@mkdir($dbDir, 0775, true) && !is_dir($dbDir)) {
            throw new RuntimeException('Unable to create database directory.');
        }

        $dbPath = $dbDir . '/blackhole.sqlite';
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');

        self::$connection = $pdo;
        return $pdo;
    }
}
