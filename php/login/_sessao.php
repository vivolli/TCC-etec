<?php
require_once __DIR__ . '/../autenticacao.php';

function getSessaoInfo(): array
{
    if (!function_exists('iniciar_sessao_segura')) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    } else {
        iniciar_sessao_segura();
    }

    $logado = false;
    $papel = null;
    $nome = null;

    if (function_exists('esta_logado') && esta_logado()) {
        $logado = true;
        $papel = $_SESSION['usuario_papel'] ?? null;
        $nome = $_SESSION['usuario_nome'] ?? null;
    }

    return ['logado' => $logado, 'papel' => $papel, 'nome' => $nome];
}
