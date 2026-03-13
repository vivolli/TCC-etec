<?php

require_once __DIR__ . '/../Core/autenticacao.php';

iniciar_sessao_segura();

if (!esta_logado()) {
    header('Location: /TCC-etec/php/login/entrar.php');
    exit;
}

requer_autenticacao();

require_once __DIR__ . '/../app/Controllers/AdminController.php';

$controller = new AdminController();
$controller->dashboard();