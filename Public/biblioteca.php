<?php

require_once __DIR__ . '/../biblioteca/_acesso.php';
require_once __DIR__ . '/../_sessao.php';

requer_aluno();

$info = getSessaoInfo();

require __DIR__ . '/../app/View/Biblioteca.php';

$controller = new BibliotecaController();
$controller->index($info);