<?php

namespace App\Core;

use Dotenv\Dotenv;

class Bootstrap
{
    public static function init(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', realpath(__DIR__ . '/../../'));
        }

        if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
            require_once BASE_PATH . '/vendor/autoload.php';
        }

        if (file_exists(BASE_PATH . '/.env')) {
            if (class_exists('Dotenv\\Dotenv')) {
                $dotenv = Dotenv::createImmutable(BASE_PATH);
                $dotenv->safeLoad();
            } else {
                trigger_error('Pacote vlucas/phpdotenv não encontrado; variáveis de ambiente não foram carregadas.', E_USER_WARNING);
            }
        }

        $timezone = config('app.timezone', 'UTC');
        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
        }

        mb_internal_encoding('UTF-8');
    }
}

if (!function_exists('app_get_pdo_global')) {
    function app_get_pdo_global()
    {
        return Database::connection();
    }
}


