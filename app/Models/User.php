<?php
namespace App\Models;

use PDO;

class User
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function id(): int
    {
        return (int)($this->data['id'] ?? 0);
    }

    public function email(): string
    {
        return (string)($this->data['email'] ?? '');
    }

    public function name(): string
    {
        return (string)($this->data['nome_completo'] ?? ($this->data['nome'] ?? ''));
    }

    public function role(): string
    {
        return strtolower((string)($this->data['papel'] ?? ''));
    }

    public function isActive(): bool
    {
        if (!isset($this->data['ativo'])) return true;
        return (bool)$this->data['ativo'];
    }

    public function passwordHash(): ?string
    {
        return $this->data['senha_hash'] ?? $this->data['senha'] ?? $this->data['password'] ?? null;
    }

    public static function findByEmail(PDO $pdo, string $email): ?self
    {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) return null;
        return new self($row);
    }
}


