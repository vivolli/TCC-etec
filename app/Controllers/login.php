<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../Core/autenticacao.php';
require_once __DIR__ . '/../../../Config/db/conexao.php';

$remember = !empty($_POST['lembrar-de-mim']);
$days     = $remember ? 7 : 1;
$lifetime = $days * 24 * 60 * 60;
$secure   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

ini_set('session.gc_maxlifetime', (string)$lifetime);

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path'     => '/',
    'secure'   => $secure,
    'httponly' => true,
    'samesite' => 'Lax'
]);

iniciar_sessao_segura();

if (
    esta_logado() &&
    in_array(strtolower((string)($_SESSION['usuario_papel'] ?? '')), ['adm', 'administrador', 'admin', 'professor', 'prof'], true)
) {
    header('Location: /TCC-etec/php/login/adms/logado/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || $senha === '') {
    header('Location: login.html?' . http_build_query([
        'error' => 'Informe e-mail e senha válidos.'
    ]));
    exit;
}

try {
    $pdo = Database::getInstance()->getConnection();

    $stmt = $pdo->prepare(
        'SELECT id, senha_hash, ativo, papel, nome_completo
         FROM usuarios
         WHERE email = ?
         LIMIT 1'
    );
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        usleep(150000);
        header('Location: login.html?' . http_build_query([
            'error' => 'E-mail ou senha inválidos.'
        ]));
        exit;
    }

    $papelNorm = strtolower(trim((string)($usuario['papel'] ?? '')));

    $papeisPermitidos = [
        'adm',
        'administrador',
        'admin',
        'professor',
        'prof',
        'docente',
        'teacher'
    ];

    if (!in_array($papelNorm, $papeisPermitidos, true)) {
        error_log(
            'Login admin: papel não autorizado | user_id=' .
            ($usuario['id'] ?? 'n/a') .
            ' | papel=' . $papelNorm
        );

        header('Location: login.html?' . http_build_query([
            'error' => 'Acesso restrito à administração.'
        ]));
        exit;
    }

    if (array_key_exists('ativo', $usuario) && !$usuario['ativo']) {
        header('Location: login.html?' . http_build_query([
            'error' => 'Conta inativa. Entre em contato com o administrador.'
        ]));
        exit;
    }

    if (!password_verify($senha, $usuario['senha_hash'] ?? '')) {
        error_log(
            'Login admin: senha inválida | user_id=' .
            ($usuario['id'] ?? 'n/a')
        );

        header('Location: login.html?' . http_build_query([
            'error' => 'E-mail ou senha inválidos.'
        ]));
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['usuario_id']    = (int)$usuario['id'];
    $_SESSION['usuario_nome']  = $usuario['nome_completo'] ?? '';
    $_SESSION['usuario_papel'] = $papelNorm;

    setcookie(
        'remember_me',
        (string)$days,
        [
            'expires'  => time() + $lifetime,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );

    header('Location: /TCC-etec/php/login/adms/logado/index.php');
    exit;

} catch (Throwable $e) {
    error_log('Login error (admin): ' . $e->getMessage());

    header('Location: login.html?' . http_build_query([
        'error' => 'Erro interno ao processar login.'
    ]));
    exit;
}
