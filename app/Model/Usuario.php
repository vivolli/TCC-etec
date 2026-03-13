<?php
/**
 * app/Model/Usuario.php
 * Model para gerenciamento de usuários
 */

namespace App\Model;

use PDO;

class Usuario
{
    private ?PDO $pdo = null;

    public function __construct(?PDO $pdo = null)
    {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            require_once __DIR__ . '/../../Config/db/conexao.php';
            $db = \Database::getInstance();
            $this->pdo = $db->getConnection();
        }
    }

    /**
     * Busca usuário por email
     */
    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = ? AND ativo = 1');
        $stmt->execute([$email]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    /**
     * Busca usuário por ID com todas as informações
     */
    public function buscarCompleto(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT u.* FROM usuarios u
            WHERE u.id = ? AND u.ativo = 1
        ');
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            return null;
        }

        // Se for aluno, carrega dados de aluno
        if ($usuario['papel'] === 'aluno') {
            $stmt = $this->pdo->prepare('
                SELECT a.*, c.nome as nome_curso
                FROM alunos a
                LEFT JOIN cursos c ON a.curso_id = c.id
                WHERE a.usuario_id = ?
            ');
            $stmt->execute([$id]);
            $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($aluno) {
                $usuario['aluno'] = $aluno;
            }
        }

        // Se for funcionário, carrega dados de funcionário
        if ($usuario['papel'] === 'funcionario') {
            $stmt = $this->pdo->prepare('
                SELECT * FROM funcionarios
                WHERE usuario_id = ?
            ');
            $stmt->execute([$id]);
            $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($funcionario) {
                $usuario['funcionario'] = $funcionario;
            }
        }

        return $usuario;
    }

    /**
     * Valida senha contra hash
     */
    public function validarSenha(string $senha, string $hash): bool
    {
        return password_verify($senha, $hash);
    }

    /**
     * Gera hash de senha
     */
    public static function gerarHashSenha(string $senha): string
    {
        return password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Registra tentativa de login
     */
    public function registrarTentativa(int $usuarioId): void
    {
        $hoje = date('Y-m-d');
        
        $stmt = $this->pdo->prepare('
            INSERT INTO tentativas_login (usuario_id, data, tentativas, ultimo_tentativa)
            VALUES (?, ?, 1, NOW())
            ON DUPLICATE KEY UPDATE
                tentativas = tentativas + 1,
                ultimo_tentativa = NOW()
        ');
        $stmt->execute([$usuarioId, $hoje]);
    }

    /**
     * Verifica se usuário está bloqueado por muitas tentativas
     */
    public function estaBloqueado(int $usuarioId, int $limiteIntentativas = 5): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT tentativas FROM tentativas_login
            WHERE usuario_id = ? AND data = CURDATE()
        ');
        $stmt->execute([$usuarioId]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado && $resultado['tentativas'] >= $limiteIntentativas;
    }

    /**
     * Limpa tentativas de login
     */
    public function limparTentativas(int $usuarioId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()');
        $stmt->execute([$usuarioId]);
    }

    /**
     * Registra ação de auditoria
     */
    public function registrarAuditoria(int $usuarioId, string $acao, ?array $meta = null): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO registro_auditoria (usuario_id, acao, meta, criado_em)
            VALUES (?, ?, ?, NOW())
        ');
        $metaJson = $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null;
        $stmt->execute([$usuarioId, $acao, $metaJson]);
    }

    /**
     * Busca notícias publicadas
     */
    public function buscarNoticias(int $limite = 10): array
    {
        $stmt = $this->pdo->prepare('
            SELECT n.*, u.nome_completo as autor_nome
            FROM noticias n
            LEFT JOIN usuarios u ON n.autor_id = u.id
            WHERE n.publicado = 1
            ORDER BY n.publicado_em DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
