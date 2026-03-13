<?php
<<<<<<<< HEAD:Biblioteca/_acesso.php
// Biblioteca/_acesso.php
// Reutilizável: exige que o usuario esteja logado e seja do papel 'aluno'.

require_once __DIR__ . '/../_sessao.php';
========
// canonical copy of php/biblioteca/_acesso.php
// Reusable: requires an aluno session and provides CSRF helpers.

// Use the canonical session helper and CSRF helpers in app/php.
require_once __DIR__ . '/../login/_sessao.php';
>>>>>>>> c20f15da362d705ddd5a772287b27b3ecff75f58:app/Legacy/php/biblioteca/_acesso.php

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
    if (!function_exists('getSessaoInfo')) {
        require_once __DIR__ . '/../_sessao.php';
    }
    
    $info = getSessaoInfo();
    $logado = (bool)($info['logado'] ?? false);
    $papel = strtolower((string)($info['papel'] ?? ''));

    if (!($logado && $papel === 'aluno')) {
        $request = $_SERVER['REQUEST_URI'] ?? '/';
<<<<<<<< HEAD:Biblioteca/_acesso.php
        $login = '/TCC-etec/Public/login.html';
========
        $login = '/TCC-etec/app/php/login/entrar.php';
>>>>>>>> c20f15da362d705ddd5a772287b27b3ecff75f58:app/Legacy/php/biblioteca/_acesso.php
        header('Location: ' . $login . '?redirect=' . urlencode($request));
        exit;
    }
}

function get_aluno_info(): array
{
    return getSessaoInfo();
}

?>


