<?php
/**
 * app/Model/Aluno.php
 * Model para gerenciamento de dados de alunos
 */

namespace App\Model;

use PDO;

class Aluno
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
     * Busca informações completas do aluno
     */
    public function buscarCompleto(int $usuarioId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                a.*,
                u.email,
                u.nome_completo,
                u.telefone,
                c.nome as nome_curso,
                c.codigo as codigo_curso
            FROM alunos a
            INNER JOIN usuarios u ON a.usuario_id = u.id
            LEFT JOIN cursos c ON a.curso_id = c.id
            WHERE a.usuario_id = ?
        ');
        $stmt->execute([$usuarioId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Busca empréstimos ativos do aluno
     */
    public function buscarEmprestimosAtivos(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                e.id,
                e.livro_id,
                e.emprestado_em,
                e.vencimento_em,
                e.status,
                e.multa_centavos,
                l.titulo,
                l.autor,
                l.editora,
                CASE 
                    WHEN e.vencimento_em < NOW() AND e.status = "emprestado" THEN "atrasado"
                    ELSE e.status
                END as status_real,
                DATEDIFF(CURDATE(), DATE(e.vencimento_em)) as dias_atraso
            FROM biblioteca_emprestimos e
            INNER JOIN biblioteca_livros l ON e.livro_id = l.id
            WHERE e.usuario_id = ? AND e.status = "emprestado"
            ORDER BY e.vencimento_em ASC
        ');
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca histórico de empréstimos
     */
    public function buscarHistoricoEmprestimos(int $usuarioId, int $limite = 20): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                e.id,
                e.livro_id,
                e.emprestado_em,
                e.vencimento_em,
                e.devolvido_em,
                e.status,
                l.titulo,
                l.autor
            FROM biblioteca_emprestimos e
            INNER JOIN biblioteca_livros l ON e.livro_id = l.id
            WHERE e.usuario_id = ?
            ORDER BY e.emprestado_em DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $usuarioId);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca turmas do aluno
     */
    public function buscarTurmas(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                t.*,
                c.nome as nome_curso,
                m.status as status_matricula,
                m.matriculado_em
            FROM matriculas m
            INNER JOIN turmas t ON m.turma_id = t.id
            INNER JOIN cursos c ON t.curso_id = c.id
            WHERE m.aluno_id = (
                SELECT id FROM alunos WHERE usuario_id = ?
            ) AND m.status = "ativo"
        ');
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca solicitações de secretaria do aluno
     */
    public function buscarSolicitacoes(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM solicitacoes_secretaria
            WHERE usuario_id = ?
            ORDER BY criado_em DESC
            LIMIT 10
        ');
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca livros disponíveis na biblioteca
     */
    public function buscarLivrosDisponiveis(int $limite = 20): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *,
                ROUND((copias_disponiveis / copias_total) * 100) as percentual_disponivel
            FROM biblioteca_livros
            WHERE copias_disponiveis > 0
            ORDER BY titulo ASC
            LIMIT ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cria nova solicitação de secretaria
     */
    public function criarSolicitacao(int $usuarioId, string $tipo, string $detalhes = ''): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO solicitacoes_secretaria (usuario_id, tipo_solicitacao, detalhes, status)
            VALUES (?, ?, ?, "aberto")
        ');
        return $stmt->execute([$usuarioId, $tipo, $detalhes]);
    }
}
