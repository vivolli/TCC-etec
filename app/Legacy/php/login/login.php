<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	require __DIR__ . '/alunos/login.php';
	exit;
}

header('Location: /TCC-etec/app/php/login/alunos/login.html');
exit;


