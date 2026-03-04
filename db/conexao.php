<?php
/*
 * Uso:
 * require_once __DIR__ . '/conexao.php';
 * $pdo = getPDO();
*/

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {

    error_log('Aviso: vendor/autoload.php não encontrado. Certifique-se de executar composer install.');
}

if (class_exists('\Dotenv\Dotenv')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();
    } catch (Exception $e) {
        error_log('Erro ao carregar .env: ' . $e->getMessage());
    }
}

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
    $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT');
    $db   = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE');
    $user = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME');
    $pass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
    $charset = $_ENV['DB_CHARSET'] ?? getenv('DB_CHARSET');

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $db, $charset);

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log('Conexão com o banco falhou: ' . $e->getMessage());
        throw $e->getMessage();
    }
}

function closePDO(): void
{
    $ref = new \ReflectionFunction('getPDO');
    $staticVars = $ref->getStaticVariables();
}

function dbQuery(string $sql, array $params = []): array|bool
{
    $pdo = getPDO();
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute($params);
    if ($ok) {
        try {
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return true;
        }
    }
    return false;
}

