<?php
declare(strict_types=1);

namespace App\Support;

final class Autoloader
{
    public static function register(string $srcPath): void
    {
        spl_autoload_register(
            static function (string $className) use ($srcPath): void {
                $prefix = 'App\\';
                if (strncmp($className, $prefix, strlen($prefix)) !== 0) {
                    return;
                }

                $relative = substr($className, strlen($prefix));
                $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
                $filePath = rtrim($srcPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $relativePath;

                if (is_file($filePath)) {
                    require $filePath;
                }
            }
        );
    }
}
