<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class LoginRateLimiter
{
    private PDO $pdo;
    private int $limiteDiario = 10;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function isBlocked(string $fingerprint): bool
    {
        $stmt = $this->pdo->prepare('SELECT tentativas FROM tentativas_login WHERE fingerprint = ? AND data = CURDATE()');
        $stmt->execute([$fingerprint]);
        $tentativas = (int)($stmt->fetchColumn() ?? 0);
        return $tentativas >= $this->limiteDiario;
    }

    public function registerFailure(string $fingerprint, array $meta = []): array
    {
        $stmt = $this->pdo->prepare('INSERT INTO tentativas_login (usuario_id, fingerprint, data, tentativas, ultimo_tentativa)
            VALUES (?, ?, CURDATE(), 1, NOW())
            ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_tentativa = NOW()');
        $stmt->execute([0, $fingerprint]);

        $remaining = $this->remainingAttempts($fingerprint);
        return ['remaining' => $remaining];
    }

    public function remainingAttempts(string $fingerprint): int
    {
        $stmt = $this->pdo->prepare('SELECT tentativas FROM tentativas_login WHERE fingerprint = ? AND data = CURDATE()');
        $stmt->execute([$fingerprint]);
        $tentativas = (int)($stmt->fetchColumn() ?? 0);
        return max(0, $this->limiteDiario - $tentativas);
    }

    public function reset(string $fingerprint): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tentativas_login WHERE fingerprint = ? AND data = CURDATE()');
        $stmt->execute([$fingerprint]);
    }
}
