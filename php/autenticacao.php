<?php
if (!function_exists('iniciar_sessao_segura')) {
    function iniciar_sessao_segura(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            $lifetime = 0;
            if (!empty($_COOKIE['remember_me'])) {
                $days = intval($_COOKIE['remember_me']);
                if ($days > 0) {
                    $lifetime = $days * 24 * 60 * 60;
                }
            }

            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            ini_set('session.use_strict_mode', '1');
            if ($lifetime > 0) {
                ini_set('session.gc_maxlifetime', (string)$lifetime);
            }
            session_start();
        }
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
            $login = '/TCC-etec/php/login/login.html';
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

?>
