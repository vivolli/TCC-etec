<?php
/**
 * _sessao.php
 * Arquivo centralizado de gerenciamento de sessão
 * Usado por APIs e outras funcionalidades
 */

require_once __DIR__ . '/Core/autenticacao.php';

if (!function_exists('getSessaoInfo')) {
    function getSessaoInfo(): array
    {
        iniciar_sessao_segura();
        
        return [
            'logado' => esta_logado(),
            'usuario_id' => (int)($_SESSION['usuario_id'] ?? 0),
            'usuario_email' => $_SESSION['usuario_email'] ?? null,
            'usuario_nome' => $_SESSION['usuario_nome'] ?? null,
            'papel' => $_SESSION['usuario_papel'] ?? null,
        ];
    }
}

if (!function_exists('requer_papel')) {
    function requer_papel(array $papeis_permitidos): void
    {
        requer_autenticacao();
        $papel = strtolower((string)($_SESSION['usuario_papel'] ?? ''));
        $papeis_normaizados = array_map('strtolower', $papeis_permitidos);
        
        if (!in_array($papel, $papeis_normaizados, true)) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode([
                'ok' => false,
                'error' => 'Acesso negado: permissão insuficiente'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}

if (!function_exists('eh_admin')) {
    function eh_admin(): bool
    {
        $papel = strtolower((string)($_SESSION['usuario_papel'] ?? ''));
        return in_array($papel, ['admin', 'adm', 'administrador'], true);
    }
}

if (!function_exists('eh_professor')) {
    function eh_professor(): bool
    {
        $papel = strtolower((string)($_SESSION['usuario_papel'] ?? ''));
        return in_array($papel, ['professor', 'prof', 'docente'], true);
    }
}

if (!function_exists('eh_aluno')) {
    function eh_aluno(): bool
    {
        $papel = strtolower((string)($_SESSION['usuario_papel'] ?? ''));
        return $papel === 'aluno';
    }
}

if (!function_exists('eh_funcionario')) {
    function eh_funcionario(): bool
    {
        $papel = strtolower((string)($_SESSION['usuario_papel'] ?? ''));
        return in_array($papel, ['secretaria', 'secretário', 'secretariao', 'secretaria_adj', 'funcionario'], true);
    }
}
