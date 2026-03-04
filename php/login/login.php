<?php

// Incluir helper de autenticação para checar sessão atual
// Redirecionador/encaminhador: delega para o novo handler de alunos
// Se receber um POST, inclui o handler de alunos para processar o login.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	require __DIR__ . '/alunos/login.php';
	exit;
}

// Caso contrário, redireciona para a nova página de login dos alunos.
header('Location: /TCC-etec/php/login/alunos/login.html');
exit;

