<?php
require_once __DIR__ . '/../../autenticacao.php';
require_once __DIR__ . '/../../../db/conexao.php';

iniciar_sessao_segura();

if (esta_logado() && ($_SESSION['usuario_papel'] ?? 'aluno') === 'aluno') {
    $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? null;
    if ($redirect && strpos($redirect, '/') === 0 && strpos($redirect, '//') !== 0) {
        header('Location: ' . $redirect);
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

if (!$email || $senha === '') {
    header('Location: login.html?error=Dados inválidos');
    exit;
}

try {
    $pdo = Database::getInstance()->getConnection();

    $colStmt = $pdo->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios'"
    );
    $colStmt->execute();
    $cols = array_map('strtolower', $colStmt->fetchAll(PDO::FETCH_COLUMN));

    $pwdCol = null;
    foreach (['senha_hash', 'senha', 'password'] as $c) {
        if (in_array($c, $cols, true)) {
            $pwdCol = $c;
            break;
        }
    }

    if (!$pwdCol) {
        header('Location: login.html?error=Erro de autenticação');
        exit;
    }

    $stmt = $pdo->prepare(
        "SELECT id, ativo, papel, nome_completo, {$pwdCol} AS senha_hash
         FROM usuarios WHERE email = ? LIMIT 1"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        usleep(150000);
        header('Location: login.html?error=E-mail ou senha inválidos');
        exit;
    }

    $papel = strtolower(trim($user['papel'] ?? ''));
    if (!in_array($papel, ['aluno', 'estudante', 'student'], true)) {
        header('Location: login.html?error=Acesso restrito');
        exit;
    }

    if (isset($user['ativo']) && !$user['ativo']) {
        header('Location: login.html?error=Conta inativa');
        exit;
    }

    $usuario_id = (int)$user['id'];

    $stmt = $pdo->prepare(
        'SELECT tentativas FROM tentativas_login 
         WHERE usuario_id = ? AND data = CURDATE()'
    );
    $stmt->execute([$usuario_id]);
    $tentativas = (int)($stmt->fetchColumn() ?? 0);

    if ($tentativas >= 5) {
        header('Location: login.html?error=Limite de tentativas atingido');
        exit;
    }

    if (password_verify($senha, $user['senha_hash'])) {
        $pdo->prepare(
            'DELETE FROM tentativas_login 
             WHERE usuario_id = ? AND data = CURDATE()'
        )->execute([$usuario_id]);

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['usuario_nome'] = $user['nome_completo'] ?? '';
        $_SESSION['usuario_papel'] = 'aluno';

        $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? null;
        if ($redirect && strpos($redirect, '/') === 0 && strpos($redirect, '//') !== 0) {
            header('Location: ' . $redirect);
        } else {
            header('Location: /TCC-etec/php/sou_aluno/index.php');
        }
        exit;
    }

    $pdo->prepare(
        'INSERT INTO tentativas_login (usuario_id, data, tentativas, ultimo_tentativa)
         VALUES (?, CURDATE(), 1, NOW())
         ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_tentativa = NOW()'
    )->execute([$usuario_id]);

    header('Location: login.html?error=Senha incorreta');
    exit;

} catch (Throwable $e) {
    header('Location: login.html?error=Erro interno');
    exit;
}
