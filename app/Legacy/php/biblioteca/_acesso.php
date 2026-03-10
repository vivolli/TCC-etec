<?php
// canonical copy of php/biblioteca/_acesso.php
// Reusable: requires an aluno session and provides CSRF helpers.

// Use the canonical session helper and CSRF helpers in app/php.
require_once __DIR__ . '/../login/_sessao.php';

function csrf_token(): string
{
    if (function_exists('iniciar_sessao_segura')) {
        iniciar_sessao_segura();
    } elseif (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        } catch (Exception $e) {
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));
        }
    }
    return (string)$_SESSION['csrf_token'];
}

function validate_csrf(?string $token): bool
{
    if (function_exists('iniciar_sessao_segura')) {
        iniciar_sessao_segura();
    } elseif (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], (string)$token);
}

function requer_aluno(): void
{
    $info = getSessaoInfo();
    $logado = (bool)($info['logado'] ?? false);
    $papel = strtolower((string)($info['papel'] ?? ''));

    if (!($logado && $papel === 'aluno')) {
        $request = $_SERVER['REQUEST_URI'] ?? '/';
        $login = '/TCC-etec/app/php/login/entrar.php';
        header('Location: ' . $login . '?redirect=' . urlencode($request));
        exit;
    }
}

function get_aluno_info(): array
{
    return getSessaoInfo();
}

?>


