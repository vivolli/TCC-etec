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
    if (in_array($papel, $alunoRoles, true)) {
        header('Location: /TCC-etec/php/sou_aluno/index.php');
        exit;
    }
}

readfile(__DIR__ . '/index.html');
