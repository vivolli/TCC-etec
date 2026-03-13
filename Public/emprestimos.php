<?php

require_once __DIR__ . '/../biblioteca/_acesso.php';
require_once __DIR__ . '/../_sessao.php';

requer_aluno();

$usuario_id = $_SESSION['usuario_id'] ?? null;

// Ver empréstimos do aluno
require __DIR__ . '/../app/View/emprestimosBiblioteca.php';