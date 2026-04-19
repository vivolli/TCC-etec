<?php

namespace App\Api\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;

class RoleMiddleware implements MiddlewareInterface
{
    private const ADMIN_ROLES = ['adm', 'administrador', 'admin'];
    private const PROFESSOR_ROLES = ['professor', 'prof', 'docente'];
    private const SECRETARIA_ROLES = ['secretaria', 'secretário', 'secretariao', 'secretaria_adj', 'funcionario'];
    private const STUDENT_ROLES = ['aluno', 'estudante', 'student'];

    private ?string $parameter;

    public function __construct(?string $parameter = null)
    {
        $this->parameter = $parameter;
    }

    public function handle(Request $request): bool
    {
        $user = $request->user ?? ($_REQUEST['auth_user'] ?? null);
        if (!is_array($user) || empty($user['role'])) {
            response_json(['ok' => false, 'message' => 'Usuário não autenticado.'], 401);
            return false;
        }

        if ($this->parameter === null || trim($this->parameter) === '') {
            return true;
        }

        $allowedRoles = array_map('trim', explode(',', $this->parameter));
        $role = strtolower((string)$user['role']);

        if (!$this->isRoleAllowed($role, $allowedRoles)) {
            response_json(['ok' => false, 'message' => 'Permissão insuficiente.'], 403);
            return false;
        }

        return true;
    }

    private function isRoleAllowed(string $role, array $allowedRoles): bool
    {
        foreach ($allowedRoles as $allowedRole) {
            $allowedRole = strtolower($allowedRole);
            if ($allowedRole === $role) {
                return true;
            }

            switch ($allowedRole) {
                case 'admin':
                    if (in_array($role, self::ADMIN_ROLES, true)) {
                        return true;
                    }
                    break;
                case 'professor':
                    if (in_array($role, self::PROFESSOR_ROLES, true)) {
                        return true;
                    }
                    break;
                case 'secretaria':
                    if (in_array($role, self::SECRETARIA_ROLES, true)) {
                        return true;
                    }
                    break;
                case 'aluno':
                case 'student':
                    if (in_array($role, self::STUDENT_ROLES, true)) {
                        return true;
                    }
                    break;
                default:
                    if ($allowedRole === $role) {
                        return true;
                    }
                    break;
            }
        }

        return false;
    }
}
