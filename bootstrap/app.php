<?php
declare(strict_types=1);

define('APP_BASE_PATH', dirname(__DIR__));

require APP_BASE_PATH . '/src/Support/Autoloader.php';

App\Support\Autoloader::register(APP_BASE_PATH . '/src');
App\Config\Env::load(APP_BASE_PATH . '/.env');
App\Config\Env::load(APP_BASE_PATH . '/.env.example', false);

$timezone = App\Config\Env::get('APP_TIMEZONE', 'UTC');
if (is_string($timezone) && $timezone !== '') {
    date_default_timezone_set($timezone);
}

App\Support\Session::start();
App\Security\Csrf::ensureToken();
App\Database\Migrator::run();
