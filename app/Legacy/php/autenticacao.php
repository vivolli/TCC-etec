<?php
// Delegate session responsibilities to App\Core\SessionManager while keeping function names
require_once __DIR__ . '/../../Core/Bootstrap.php';
use App\Core\SessionManager;

if (!function_exists('iniciar_sessao_segura')) {
    function iniciar_sessao_segura(): void
    {
        $sm = SessionManager::getInstance();
        $sm->start();
    }
}

if (!function_exists('esta_logado')) {
    function esta_logado(): bool
    {
        $sm = SessionManager::getInstance();
        $info = $sm->getSessionInfo();
        return !empty($info['logado']);
    }
}

if (!function_exists('requer_autenticacao')) {
    function requer_autenticacao(): void
    {
        $sm = SessionManager::getInstance();
        $sm->start();
        if (empty($_SESSION['usuario_id'])) {
            $request = $_SERVER['REQUEST_URI'] ?? '/';
            $login = '/TCC-etec/app/php/login/entrar.php';
            header('Location: ' . $login . '?redirect=' . urlencode($request));
            exit;
        }
    }
}

if (!function_exists('encerrar_sessao')) {
    function encerrar_sessao(): void
    {
        $sm = SessionManager::getInstance();
        $sm->destroy();
    }
}

?>


