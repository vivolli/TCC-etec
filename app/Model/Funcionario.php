<?php
/**
 * app/Model/Funcionario.php
 * Model para gerenciamento de dados de funcionários
 */

namespace App\Model;

use PDO;

class Funcionario
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
     * Busca informações completas do funcionário
     */
    public function buscarCompleto(int $usuarioId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                f.*,
                u.email,
                u.nome_completo,
                u.telefone,
                u.papel,
                u.criado_em
            FROM funcionarios f
            INNER JOIN usuarios u ON f.usuario_id = u.id
            WHERE f.usuario_id = ?
        ');
        $stmt->execute([$usuarioId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Lista todas as solicitações de secretaria (apenas para funcionários)
     */
    public function buscarSolicitacoes(int $limite = 50, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                s.*,
                u.nome_completo,
                u.email
            FROM solicitacoes_secretaria s
            INNER JOIN usuarios u ON s.usuario_id = u.id
            ORDER BY s.criado_em DESC
            LIMIT ? OFFSET ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca solicitações por status
     */
    public function buscarSolicitacoesPorStatus(string $status, int $limite = 50): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                s.*,
                u.nome_completo,
                u.email
            FROM solicitacoes_secretaria s
            INNER JOIN usuarios u ON s.usuario_id = u.id
            WHERE s.status = ?
            ORDER BY s.criado_em DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $status);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza status de solicitação
     */
    public function atualizarStatusSolicitacao(int $solicitacaoId, string $novoStatus): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE solicitacoes_secretaria
            SET status = ?, atualizado_em = NOW()
            WHERE id = ?
        ');
        return $stmt->execute([$novoStatus, $solicitacaoId]);
    }

    /**
     * Busca empréstimos atrasados
     */
    public function buscarEmprestimosAtrasados(int $limite = 50): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                e.*,
                u.nome_completo,
                u.email,
                l.titulo,
                l.autor,
                DATEDIFF(CURDATE(), DATE(e.vencimento_em)) as dias_atraso,
                CASE 
                    WHEN DATEDIFF(CURDATE(), DATE(e.vencimento_em)) <= 7 THEN "alerta"
                    WHEN DATEDIFF(CURDATE(), DATE(e.vencimento_em)) <= 30 THEN "aviso"
                    ELSE "critico"
                END as prioridade
            FROM biblioteca_emprestimos e
            INNER JOIN usuarios u ON e.usuario_id = u.id
            INNER JOIN biblioteca_livros l ON e.livro_id = l.id
            WHERE e.status = "emprestado" AND e.vencimento_em < NOW()
            ORDER BY dias_atraso DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca estatísticas da biblioteca
     */
    public function obterEstatisticasBiblioteca(): array
    {
        $stats = [];

        // Total de livros
        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total, SUM(copias_disponiveis) as disponiveis
            FROM biblioteca_livros
        ');
        $stats['livros'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Empréstimos ativos
        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total FROM biblioteca_emprestimos WHERE status = "emprestado"
        ');
        $stats['emprestimos_ativos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Empréstimos atrasados
        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total FROM biblioteca_emprestimos
            WHERE status = "emprestado" AND vencimento_em < NOW()
        ');
        $stats['emprestimos_atrasados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Usuários
        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total FROM usuarios WHERE ativo = 1
        ');
        $stats['usuarios_ativos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }

    /**
     * Lista alunos com opção de filtro
     */
    public function listarAlunos(int $limite = 50, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                u.id,
                u.nome_completo,
                u.email,
                u.telefone,
                u.criado_em,
                a.matricula,
                a.turma,
                c.nome as nome_curso
            FROM usuarios u
            LEFT JOIN alunos a ON u.id = a.usuario_id
            LEFT JOIN cursos c ON a.curso_id = c.id
            WHERE u.papel = "aluno" AND u.ativo = 1
            ORDER BY u.nome_completo ASC
            LIMIT ? OFFSET ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca noticias publicadas
     */
    public function buscarNoticias(int $limite = 20): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                n.*,
                u.nome_completo as autor_nome
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
