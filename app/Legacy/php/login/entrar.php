<?php
require_once __DIR__ . '/../autenticacao.php';

function redirecionar(string $destino): void
{
    header('Location: ' . $destino);
    exit;
}

iniciar_sessao_segura();

$papel = strtolower((string)($_SESSION['usuario_papel'] ?? ''));
$adminRoles = ['adm', 'administrador', 'admin', 'professor', 'prof', 'docente'];
$secretariaRoles = ['secretaria', 'secretário', 'secretariao', 'secretaria_adj'];
$alunoRoles = ['aluno', 'estudante', 'student'];

if (esta_logado()) {
    if (in_array($papel, $adminRoles, true)) {
        redirecionar('/TCC-etec/app/php/login/adms/logado/index.php');
    }
    if (in_array($papel, $secretariaRoles, true)) {
        redirecionar('/TCC-etec/app/php/secretaria/secretaria.php');
    }
    if (in_array($papel, $alunoRoles, true)) {
        redirecionar('/TCC-etec/app/php/sou_aluno/index.php');
    }
}

$perfil = strtolower((string)($_GET['perfil'] ?? ''));
if (in_array($perfil, ['adm', 'administrador', 'admin', 'professor', 'prof'], true)) {
    redirecionar('/TCC-etec/app/php/login/adms/login.html');
}

if (in_array($perfil, ['secretaria', 'secretario', 'secretária'], true)) {
    redirecionar('/TCC-etec/app/php/secretaria/secretaria.php');
}

// fallback ao login de alunos
redirecionar('/TCC-etec/app/php/login/alunos/login.html');


