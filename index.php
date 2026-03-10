<?php
require_once __DIR__ . '/php/autenticacao.php';

iniciar_sessao_segura();
$papel = strtolower((string)($_SESSION['usuario_papel'] ?? ''));

$adminRoles = ['adm', 'administrador', 'admin', 'professor', 'prof', 'docente'];
$secretariaRoles = ['secretaria', 'secretário', 'secretariao', 'secretaria_adj'];
$alunoRoles = ['aluno', 'estudante', 'student'];

if (esta_logado()) {
    if (in_array($papel, $adminRoles, true)) {
        header('Location: /TCC-etec/php/login/adms/logado/index.php');
        exit;
    }
    if (in_array($papel, $secretariaRoles, true)) {
        header('Location: /TCC-etec/php/secretaria/secretaria.php');
        exit;
    }
    // Não redirecionar automaticamente usuários 'aluno' para uma página separada;
    // permitir que o índice comum do site seja exibido para todos os perfis.
}

readfile(__DIR__ . '/index.html');
