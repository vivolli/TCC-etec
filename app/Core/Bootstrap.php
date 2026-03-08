<?php
namespace App\Core;

if (!defined('APP_ROOT')) {
    define('APP_ROOT', realpath(__DIR__ . '/../../'));
}

// Attempt to load vendor/autoload if available
if (file_exists(APP_ROOT . '/vendor/autoload.php')) {
    require_once APP_ROOT . '/vendor/autoload.php';
}

// Ensure existing DB helper is available (keeps compatibility)
if (file_exists(APP_ROOT . '/db/conexao.php')) {
    require_once APP_ROOT . '/db/conexao.php';
}

// PSR-4-ish autoloader for App\ namespace (simple)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) return;
    $relative = substr($class, strlen($prefix));
    $path = APP_ROOT . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($path)) require_once $path;
});

function app_get_pdo()
{
    // prefer existing getPDO()
    if (function_exists('getPDO')) return getPDO();
    // otherwise attempt to build a PDO from env
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $db   = getenv('DB_DATABASE') ?: 'tcc';
    $user = getenv('DB_USERNAME') ?: 'root';
    $pass = getenv('DB_PASSWORD') ?: '';
    $charset = getenv('DB_CHARSET') ?: 'utf8mb4';
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $db, $charset);
    try {
        return new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (\Throwable $e) {
        error_log('app_get_pdo error: ' . $e->getMessage());
        return null;
    }
}

// global helper wrapper for legacy code
if (!function_exists('app_get_pdo_global')) {
    function app_get_pdo_global() {
        return \App\Core\app_get_pdo();
    }
}


