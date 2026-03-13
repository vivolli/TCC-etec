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
<<<<<<< HEAD
    if (in_array($papel, $alunoRoles, true)) {
        header('Location: /TCC-etec/Public/aluno.php');
        exit;
    }
=======
    // Não redirecionar automaticamente usuários 'aluno' para uma página separada;
    // permitir que o índice comum do site seja exibido para todos os perfis.
>>>>>>> c20f15da362d705ddd5a772287b27b3ecff75f58
}

readfile(__DIR__ . '/Public/index.html');

