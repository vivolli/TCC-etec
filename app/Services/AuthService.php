<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    private User $users;
    private LoginRateLimiter $limiter;

    private const ADMIN_ROLES = ['adm', 'administrador', 'admin'];
    private const PROFESSOR_ROLES = ['professor', 'prof', 'docente'];
    private const SECRETARIA_ROLES = ['secretaria', 'secretário', 'secretariao', 'secretaria_adj', 'funcionario'];
    private const STUDENT_ROLES = ['aluno', 'estudante', 'student'];

    public function __construct(?User $users = null, ?LoginRateLimiter $limiter = null)
    {
        $this->users = $users ?? new User();
        $this->limiter = $limiter ?? new LoginRateLimiter();
    }

    public static function fingerprintFromGlobals(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        return hash('sha256', $ip . '|' . $ua);
    }

    public function sanitizeRedirect(?string $requested, string $fallback = '/TCC-etec/'): string
    {
        if ($requested && str_starts_with($requested, '/TCC-etec/')) {
            return $requested;
        }
        return $fallback;
    }

    public function attempt(string $email, string $password, ?string $profile, string $fingerprint): array
    {
        $email = trim($email);
        if ($email === '' || $password === '') {
            return [
                'ok' => false,
                'message' => 'Email e senha são obrigatórios.',
            ];
        }

        if ($this->limiter->isBlocked($fingerprint)) {
            return [
                'ok' => false,
                'message' => 'Você excedeu o limite diário de tentativas de login. Aguarde algumas horas ou procure a secretaria.',
            ];
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            $meta = $this->limiter->registerFailure($fingerprint, ['email' => $email]);
            return [
                'ok' => false,
                'message' => $this->formatAttemptMessage('Email ou senha incorretos.', $meta['remaining']),
            ];
        }

        $role = strtolower((string)($user['papel'] ?? ''));
        if (!$this->profileMatches($profile, $role)) {
            $this->users->recordAttempt((int)$user['id']);
            $meta = $this->limiter->registerFailure($fingerprint, ['email' => $email]);
            return [
                'ok' => false,
                'message' => $this->formatAttemptMessage('Perfil selecionado não está autorizado para este usuário.', $meta['remaining']),
            ];
        }

        if ($this->users->isBlocked((int)$user['id'])) {
            $this->users->recordAttempt((int)$user['id']);
            $meta = $this->limiter->registerFailure($fingerprint, ['email' => $email]);
            return [
                'ok' => false,
                'message' => $this->formatAttemptMessage('Conta temporariamente bloqueada. Tente novamente mais tarde.', $meta['remaining']),
            ];
        }

        if (!$this->users->validatePassword($password, (string)($user['senha_hash'] ?? ''))) {
            $this->users->recordAttempt((int)$user['id']);
            $meta = $this->limiter->registerFailure($fingerprint, ['email' => $email]);
            return [
                'ok' => false,
                'message' => $this->formatAttemptMessage('Email ou senha incorretos.', $meta['remaining']),
            ];
        }

        $this->users->clearAttempts((int)$user['id']);
        $this->limiter->reset($fingerprint);

        return [
            'ok' => true,
            'user' => [
                'id' => (int)$user['id'],
                'email' => $user['email'],
                'nome' => $user['nome_completo'] ?? $user['nome'] ?? null,
                'papel' => $user['papel'],
            ],
            'audit' => [
                'user_id' => (int)$user['id'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'perfil' => $profile ?? 'auto',
            ],
        ];
    }

    public function profileMatches(?string $profile, string $role): bool
    {
        if ($profile === null || $profile === '') {
            return true;
        }

        $profile = strtolower($profile);
        $role = strtolower($role);

        switch ($profile) {
            case 'admin':
                return in_array($role, self::ADMIN_ROLES, true);
            case 'professor':
                return in_array($role, self::PROFESSOR_ROLES, true);
            case 'secretaria':
                return in_array($role, self::SECRETARIA_ROLES, true);
            case 'aluno':
                return in_array($role, self::STUDENT_ROLES, true);
            default:
                return true;
        }
    }

    public function defaultRedirectForRole(string $role): string
    {
        $role = strtolower($role);
        if (in_array($role, self::ADMIN_ROLES, true)) {
            return '/TCC-etec/admin';
        }
        if (in_array($role, self::PROFESSOR_ROLES, true)) {
            return '/TCC-etec/professor';
        }
        if (in_array($role, self::SECRETARIA_ROLES, true)) {
            return '/TCC-etec/secretaria';
        }
        if (in_array($role, self::STUDENT_ROLES, true)) {
            return '/TCC-etec/aluno';
        }
        return '/TCC-etec/';
    }

    public function formatAttemptMessage(string $base, int $remainingAttempts): string
    {
        $remaining = max(0, $remainingAttempts);
        return rtrim($base) . ' Tentativas restantes hoje: ' . $remaining . '.';
    }

    public function getUserModel(): User
    {
        return $this->users;
    }
}


