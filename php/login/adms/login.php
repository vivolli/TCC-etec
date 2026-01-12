<?php
require_once __DIR__ . '/../../autenticacao.php';
require_once __DIR__ . '/../../../db/conexao.php';

iniciar_sessao_segura();
if (esta_logado() && in_array(($_SESSION['usuario_papel'] ?? ''), ['adm','administrador'])) {
    header('Location: /TCC-etec/php/secretaria/secretaria.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email) {
    header('Location: login.html?' . http_build_query(['error' => 'Informe um e-mail válido.']));
    exit;
}

try {
    $pdo = getPDO();

    $stmt = $pdo->prepare('SELECT id, senha_hash, ativo, papel, nome_completo FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        usleep(150000);
        header('Location: login.html?' . http_build_query(['error' => 'E-mail ou senha inválidos.']));
        exit;
    }

    $papel = $user['papel'] ?? '';
    if (!in_array($papel, ['adm','administrador'])) {
        header('Location: login.html?' . http_build_query(['error' => 'Acesso restrito à administração.']));
        exit;
    }

    $usuario_id = (int)$user['id'];

    $hash = $user['senha_hash'] ?? null;
    if ($hash && password_verify($senha, $hash)) {
        $remember = !empty($_POST['lembrar-de-mim']);
        $days = $remember ? 7 : 1;
        $lifetime = $days * 24 * 60 * 60;
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        ini_set('session.gc_maxlifetime', (string)$lifetime);
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        setcookie('remember_me', (string)$days, time() + $lifetime, '/', '', $secure, true);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['usuario_nome'] = $user['nome_completo'] ?? '';
        $_SESSION['usuario_papel'] = 'adm';

        header('Location: /TCC-etec/php/secretaria/secretaria.php');
        exit;
    }

    header('Location: login.html?' . http_build_query(['error' => 'E-mail ou senha inválidos.']));
    exit;

} catch (Exception $e) {
    error_log('Login error (adms): ' . $e->getMessage());
    header('Location: login.html?' . http_build_query(['error' => 'Erro interno ao processar login.']));
    exit;
}
