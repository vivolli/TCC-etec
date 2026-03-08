<?php
require_once __DIR__ . '/../php/autenticacao.php';

iniciar_sessao_segura();
$papel = strtolower((string)($_SESSION['usuario_papel'] ?? ''));

$adminRoles = ['adm', 'administrador', 'admin', 'professor', 'prof', 'docente'];
$secretariaRoles = ['secretaria', 'secretário', 'secretariao', 'secretaria_adj'];
$alunoRoles = ['aluno', 'estudante', 'student'];

if (esta_logado()) {
    if (in_array($papel, $adminRoles, true)) {
        header('Location: /TCC-etec/app/php/login/adms/logado/index.php');
        exit;
    }
    if (in_array($papel, $secretariaRoles, true)) {
        header('Location: /TCC-etec/app/php/secretaria/secretaria.php');
        exit;
    }
    if (in_array($papel, $alunoRoles, true)) {
        header('Location: /TCC-etec/app/php/sou_aluno/index.php');
        exit;
    }
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if (preg_match('#/Noticias/([^/]+)(?:\.html)?$#i', $path, $m)) {
    $name = $m[1];
    $ctrl = __DIR__ . '/../Controllers/NoticiasController.php';
    if (file_exists($ctrl)) {
        require_once $ctrl;
        NoticiasController::dispatch($name);
        exit;
    }
}

if (stripos($path, '/FaleConosco') !== false) {
    $ctrl = __DIR__ . '/../Controllers/FaleConoscoController.php';
    if (file_exists($ctrl)) {
        require_once $ctrl;
        FaleConoscoController::handle();
        exit;
    }
}

readfile(__DIR__ . '/index.html');


