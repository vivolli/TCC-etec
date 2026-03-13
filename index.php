<?php
require_once __DIR__ . '/Core/autenticacao.php';

iniciar_sessao_segura();
$papel = strtolower((string)($_SESSION['usuario_papel'] ?? ''));

$adminRoles = ['adm', 'administrador', 'admin', 'professor', 'prof', 'docente'];
$secretariaRoles = ['secretaria', 'secretário', 'secretariao', 'secretaria_adj'];
$alunoRoles = ['aluno', 'estudante', 'student'];

if (esta_logado()) {
    if (in_array($papel, $adminRoles, true)) {
        header('Location: /TCC-etec/Public/admin.php');
        exit;
    }
    if (in_array($papel, $secretariaRoles, true)) {
        header('Location: /TCC-etec/Public/secretaria.php');
        exit;
    }
    if (in_array($papel, $alunoRoles, true)) {
        header('Location: /TCC-etec/Public/aluno.php');
        exit;
    }
}

readfile(__DIR__ . '/Public/index.html');

