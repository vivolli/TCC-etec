<?php

namespace App\Support;

/**
 * RoleManager - Gerenciador centralizado de papéis e permissões
 * 
 * Única fonte de verdade para definições de roles no projeto.
 * Evita duplicação e garante consistência em toda a aplicação.
 */
class RoleManager
{
    private const ADMIN_ROLES = ['adm', 'administrador', 'admin'];
    private const PROFESSOR_ROLES = ['professor', 'prof', 'docente'];
    private const SECRETARIA_ROLES = ['secretaria', 'secretário', 'secretariao', 'secretaria_adj', 'funcionario'];
    private const STUDENT_ROLES = ['aluno', 'estudante', 'student'];

    /**
     * Obtém todos os papéis válidos para administradores
     */
    public static function getAdminRoles(): array
    {
        return self::ADMIN_ROLES;
    }

    /**
     * Obtém todos os papéis válidos para professores
     */
    public static function getProfessorRoles(): array
    {
        return self::PROFESSOR_ROLES;
    }

    /**
     * Obtém todos os papéis válidos para secretária
     */
    public static function getSecretariaRoles(): array
    {
        return self::SECRETARIA_ROLES;
    }

    /**
     * Obtém todos os papéis válidos para alunos
     */
    public static function getStudentRoles(): array
    {
        return self::STUDENT_ROLES;
    }

    /**
     * Verifica se um papel/role é um administrador
     */
    public static function isAdmin(string $role): bool
    {
        return in_array(strtolower($role), self::ADMIN_ROLES, true);
    }

    /**
     * Verifica se um papel/role é um professor
     */
    public static function isProfessor(string $role): bool
    {
        return in_array(strtolower($role), self::PROFESSOR_ROLES, true);
    }

    /**
     * Verifica se um papel/role é de secretária
     */
    public static function isSecretaria(string $role): bool
    {
        return in_array(strtolower($role), self::SECRETARIA_ROLES, true);
    }

    /**
     * Verifica se um papel/role é um aluno
     */
    public static function isStudent(string $role): bool
    {
        return in_array(strtolower($role), self::STUDENT_ROLES, true);
    }

    /**
     * Verifica se um paper/role corresponde ao perfil solicitado
     */
    public static function profileMatches(?string $profile, string $role): bool
    {
        // Se perfil não foi especificado, é inválido (deve selecionar)
        if ($profile === null || $profile === '') {
            return false; // ✅ Mudado de true para false - exigir seleção
        }

        $profile = strtolower($profile);
        $role = strtolower($role);

        return match($profile) {
            'admin' => self::isAdmin($role),
            'professor' => self::isProfessor($role),
            'secretaria' => self::isSecretaria($role),
            'aluno', 'student' => self::isStudent($role),
            default => false,
        };
    }

    /**
     * Obtém a URL de redirecionamento padrão para um role
     */
    public static function getDefaultRedirectForRole(string $role): string
    {
        $role = strtolower($role);

        return match(true) {
            self::isAdmin($role) => '/TCC-etec/admin',
            self::isProfessor($role) => '/TCC-etec/professor',
            self::isSecretaria($role) => '/TCC-etec/secretaria',
            self::isStudent($role) => '/TCC-etec/aluno',
            default => '/TCC-etec/',
        };
    }

    /**
     * Normaliza um papel/role removendo espaços e convertendo para lowercase
     */
    public static function normalize(string $role): string
    {
        return strtolower(trim($role));
    }

    /**
     * Obtém o grupo de papéis a que um role pertence
     */
    public static function getGroup(string $role): ?string
    {
        $role = strtolower($role);

        return match(true) {
            self::isAdmin($role) => 'admin',
            self::isProfessor($role) => 'professor',
            self::isSecretaria($role) => 'secretaria',
            self::isStudent($role) => 'student',
            default => null,
        };
    }

    /**
     * Verifica se um role está em um grupo específico
     */
    public static function isInGroup(string $role, string $group): bool
    {
        return self::getGroup($role) === strtolower($group);
    }
}
