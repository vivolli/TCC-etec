<?php
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}
if (class_exists('\Dotenv\Dotenv')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->safeLoad();
    } catch (Throwable $e) {
    }
}

$debugSecret = $_ENV['DEBUG_SECRET'] ?? getenv('DEBUG_SECRET') ?: null;
$remote = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if ($debugSecret) {
    $provided = $_GET['secret'] ?? null;
    if (!hash_equals((string)$debugSecret, (string)$provided)) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'forbidden', 'reason' => 'invalid debug secret'], JSON_UNESCAPED_UNICODE);
        exit;
    }
} else {
    if (!in_array($remote, ['127.0.0.1', '::1'])) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'forbidden', 'remote' => $remote, 'note' => 'set DEBUG_SECRET in .env to enable remote access'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

require_once __DIR__ . '/../../db/conexao.php';
header('Content-Type: application/json; charset=utf-8');

$email = $_GET['email'] ?? null;
if (!$email) {
    echo json_encode(['error' => 'missing email', 'usage' => '?email=you@domain'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = Database::getInstance()->getConnection();
    $sql = "SELECT id, email, papel, COALESCE(senha_hash, senha, `password`) AS senha_hash, senha, `password` FROM usuarios WHERE email = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['found' => false, 'email' => $email], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $ret = [
        'found' => true,
        'id' => $user['id'] ?? null,
        'email' => $user['email'] ?? null,
        'papel' => $user['papel'] ?? null,
    ];

    $rawHash = $user['senha_hash'] ?? null;
    if ($rawHash) {
        $ret['senha_hash_present'] = true;
        $ret['senha_hash_preview'] = substr($rawHash, 0, 6) . str_repeat('*', max(6, strlen($rawHash) - 6));
    } else {
        $ret['senha_hash_present'] = false;
    }

    $ret['columns'] = [
        'senha' => isset($user['senha']) ? 'present' : 'absent',
        'password' => isset($user['password']) ? 'present' : 'absent',
    ];

    echo json_encode($ret, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
