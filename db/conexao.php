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
if (class_exists('\PDO')) {
    class Database
    {
        private static ?Database $instance = null;
        private ?PDO $pdo = null;

        private function __construct()
        {
            $this->connect();
        }

        private function connect(): void
        {
            if ($this->pdo instanceof PDO) return;

            $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
            $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT');
            $db   = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE');
            $user = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME');
            $pass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
            $charset = $_ENV['DB_CHARSET'] ?? getenv('DB_CHARSET') ?: 'utf8mb4';

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $db, $charset);

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->pdo = new PDO($dsn, $user, $pass, $options);
        }

        public static function getInstance(): Database
        {
            if (self::$instance === null) {
                self::$instance = new Database();
            }
            return self::$instance;
        }

        public function getConnection(): PDO
        {
            if (!$this->pdo instanceof PDO) $this->connect();
            return $this->pdo;
        }

        public function query(string $sql, array $params = []): array|bool
        {
            $stmt = $this->getConnection()->prepare($sql);
            $ok = $stmt->execute($params);
            if ($ok) {
                try {
                    return $stmt->fetchAll();
                } catch (\Throwable $e) {
                    return true;
                }
            }
            return false;
        }

        public static function close(): void
        {
            self::$instance = null;
        }
    }


    function getPDO(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    function closePDO(): void
    {
        Database::close();
    }

    function dbQuery(string $sql, array $params = []): array|bool
    {
        return Database::getInstance()->query($sql, $params);
    }
}


