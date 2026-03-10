<?php
namespace App\Services;

use App\Models\User;
use PDO;
use App\Core\SessionManager;

class AuthService
{
    private PDO $pdo;
    private SessionManager $session;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->session = SessionManager::getInstance();
    }

    public function attemptLogin(string $email, string $password, string $requiredRole = 'aluno'): array
    {
        $email = trim(strtolower($email));
        $user = User::findByEmail($this->pdo, $email);
        if ($user === null) {
            usleep(150000);
            return ['ok' => false, 'error' => 'E-mail ou senha inválidos'];
        }

        if ($user->role() !== $requiredRole && !($requiredRole === 'aluno' && in_array($user->role(), ['aluno','estudante','student'], true))) {
            return ['ok' => false, 'error' => 'Acesso restrito'];
        }

        if (!$user->isActive()) {
            return ['ok' => false, 'error' => 'Conta inativa'];
        }

        $userId = $user->id();

        try {
            $stmt = $this->pdo->prepare('SELECT tentativas FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()');
            $stmt->execute([$userId]);
            $attempts = (int)($stmt->fetchColumn() ?? 0);
        } catch (\Throwable $e) {
            $attempts = 0;
        }

        if ($attempts >= 5) {
            return ['ok' => false, 'error' => 'Limite de tentativas atingido'];
        }

        $hash = $user->passwordHash();
        if ($hash && password_verify($password, $hash)) {
            try {
                $this->pdo->prepare('DELETE FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()')->execute([$userId]);
            } catch (\Throwable $e) {
            }

            $this->session->regenerate();
            $this->session->set('usuario_id', $userId);
            $this->session->set('usuario_nome', $user->name());
            $this->session->set('usuario_papel', $requiredRole);

            return ['ok' => true, 'user' => $user];
        }

        try {
            $this->pdo->prepare(
                'INSERT INTO tentativas_login (usuario_id, data, tentativas, ultimo_tentativa)
                 VALUES (?, CURDATE(), 1, NOW())
                 ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_tentativa = NOW()'
            )->execute([$userId]);
        } catch (\Throwable $e) {
        }

        return ['ok' => false, 'error' => 'E-mail ou senha inválidos'];
    }

    public function logout(): void
    {
        $this->session->destroy();
    }
}


