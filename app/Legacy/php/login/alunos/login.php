<?php
// This file originally contained the procedural login logic. It now delegates to the app AuthController.
require_once __DIR__ . '/../../../../app/Core/Bootstrap.php';

use App\Controllers\AuthController;

try {
    $controller = new AuthController();
    $controller->handleAlunoLogin($_POST, $_GET);
} catch (Throwable $e) {
    error_log('AuthController error (legacy entry): ' . $e->getMessage());
    header('Location: /TCC-etec/app/php/login/alunos/login.html?error=Erro interno');
    exit;
}


