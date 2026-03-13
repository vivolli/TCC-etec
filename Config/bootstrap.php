<?php
/**
 * Config/bootstrap.php
 * Arquivo de inicialização do projeto
 * Inclua este arquivo no início de cada página: require_once __DIR__ . '/../Config/bootstrap.php';
 */

// Define constante do diretório raiz
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

// Carrega autoload
$autoloadPath = PROJECT_ROOT . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
} else {
    // Fallback para autoload manual se o arquivo gerado não existir
    function autoload_classes($class) {
        $namespaces = [
            'App\\Model\\' => 'app/Model/',
            'App\\Controller\\' => 'app/Controller/',
            'App\\View\\' => 'app/View/',
            'App\\' => 'app/',
            'Core\\' => 'Core/',
        ];

        foreach ($namespaces as $namespace => $path) {
            if (strpos($class, $namespace) === 0) {
                $relativeClass = substr($class, strlen($namespace));
                $file = PROJECT_ROOT . '/' . $path . str_replace('\\', '/', $relativeClass) . '.php';
                
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    }
    
    spl_autoload_register('autoload_classes');
}

// Carrega configuração de banco de dados se não tiver sido carregada
if (!class_exists('Database')) {
    $dbPath = PROJECT_ROOT . '/Config/db/conexao.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
    }
}

// Carrega autenticação se não tiver sido carregada
if (!function_exists('iniciar_sessao_segura')) {
    $authPath = PROJECT_ROOT . '/Core/autenticacao.php';
    if (file_exists($authPath)) {
        require_once $authPath;
    }
}

// Carrega sessão se não tiver sido carregada
if (!function_exists('getSessaoInfo')) {
    $sessaoPath = PROJECT_ROOT . '/_sessao.php';
    if (file_exists($sessaoPath)) {
        require_once $sessaoPath;
    }
}
