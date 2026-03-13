<?php

require_once __DIR__ . '/../biblioteca/_acesso.php';
require_once __DIR__ . '/../_sessao.php';

requer_aluno();

// Ver catálogo de livros
require __DIR__ . '/../app/View/CatalogoBiblioteca.php';