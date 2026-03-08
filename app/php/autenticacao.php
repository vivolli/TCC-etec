<?php
// copied from php/autenticacao.php
// backward-compatible shim: load implementation from app/Legacy if present
if (file_exists(__DIR__ . '/../Legacy/php/autenticacao.php')) {
    require_once __DIR__ . '/../Legacy/php/autenticacao.php';
} else {
    // Fallback: minimal procedural implementation
    if (!function_exists('iniciar_sessao_segura')) {
        function iniciar_sessao_segura(): void
        {
            if (session_status() === PHP_SESSION_NONE) session_start();
        }
    }
    if (!function_exists('esta_logado')) {
        function esta_logado(): bool
        {
            iniciar_sessao_segura();
            return !empty($_SESSION['usuario_id']);
        }
    }
    if (!function_exists('requer_autenticacao')) {
        function requer_autenticacao(): void
        {
            iniciar_sessao_segura();
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
            iniciar_sessao_segura();
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params['path'] ?? '/',
                    $params['domain'] ?? '',
                    $params['secure'] ?? false,
                    $params['httponly'] ?? true
                );
            }
            session_destroy();
        }
    }
}


