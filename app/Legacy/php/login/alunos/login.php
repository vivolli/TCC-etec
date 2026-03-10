<?php
// canonical copy of php/login/alunos/login.php
// Use the canonical bootstrap / controller implementation.
require_once __DIR__ . '/../../Core/Bootstrap.php';

use App\Controllers\AuthController;

try {
    $controller = new AuthController();
    $controller->handleAlunoLogin($_POST, $_GET);
} catch (Throwable $e) {
    error_log('AuthController error: ' . $e->getMessage());
    header('Location: login.html?error=Erro interno');
    exit;
}


