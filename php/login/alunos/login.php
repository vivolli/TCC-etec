<?php
require_once __DIR__ . '/../../autenticacao.php';
require_once __DIR__ . '/../../../db/conexao.php';

iniciar_sessao_segura();
if (esta_logado() && ($_SESSION['usuario_papel'] ?? 'aluno') === 'aluno') {
    $redirectParam = $_POST['redirect'] ?? $_GET['redirect'] ?? null;
    if ($redirectParam && strpos($redirectParam, '/') === 0 && strpos($redirectParam, '//') !== 0) {
        header('Location: ' . $redirectParam);
    } else {
        header('Location: /TCC-etec/php/sou_aluno/index.php');
    }
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

    $papel = $user['papel'] ?? 'aluno';
    if ($papel !== 'aluno') {
        header('Location: login.html?' . http_build_query(['error' => 'Acesso restrito à área de alunos. Use o login de ADM.']));
        exit;
    }

    $usuario_id = (int)$user['id'];
    $max_tentativas = 5;

    $stmt = $pdo->prepare('SELECT tentativas FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()');
    $stmt->execute([$usuario_id]);
    $row = $stmt->fetch();
    $tentativas = $row ? (int)$row['tentativas'] : 0;

    if ($tentativas >= $max_tentativas) {
        header('Location: login.html?' . http_build_query(['error' => "Limite de {$max_tentativas} tentativas por dia atingido. Se você não lembra a senha, solicite a redefinição."]));
        exit;
    }

    $hash = $user['senha_hash'] ?? null;
    if ($hash && password_verify($senha, $hash)) {
        $stmt = $pdo->prepare('DELETE FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()');
        $stmt->execute([$usuario_id]);

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
        $_SESSION['usuario_papel'] = 'aluno';

        $redirectParam = $_POST['redirect'] ?? $_GET['redirect'] ?? null;
        if ($redirectParam && strpos($redirectParam, '/') === 0 && strpos($redirectParam, '//') !== 0) {
            header('Location: ' . $redirectParam);
            exit;
        }
        header('Location: /TCC-etec/php/sou_aluno/index.php');
        exit;
    }

    $upsert = $pdo->prepare(
        'INSERT INTO tentativas_login (usuario_id, data, tentativas, ultimo_tentativa) VALUES (?, CURDATE(), 1, NOW()) '
        . 'ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_tentativa = NOW()'
    );
    $upsert->execute([$usuario_id]);

    $stmt = $pdo->prepare('SELECT tentativas FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()');
    $stmt->execute([$usuario_id]);
    $row = $stmt->fetch();
    $tentativas = $row ? (int)$row['tentativas'] : 0;
    $restantes = max(0, $max_tentativas - $tentativas);

    $errorMsg = 'Senha incorreta. Este e-mail existe no sistema; se não lembrar a senha, solicite redefinição. Tentativas restantes hoje: ' . $restantes . '.';
    header('Location: login.html?' . http_build_query(['error' => $errorMsg]));
    exit;

} catch (Exception $e) {
    error_log('Login error (alunos): ' . $e->getMessage());
    header('Location: login.html?' . http_build_query(['error' => 'Erro interno ao processar login.']));
    exit;
}
