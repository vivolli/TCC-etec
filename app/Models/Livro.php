<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Livro
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function listar(int $limite = 200): array
    {
        $stmt = $this->pdo->prepare('
            SELECT id, titulo, autor, ano, disponivel, copias_disponiveis
            FROM biblioteca_livros
            WHERE disponivel = 1 OR copias_disponiveis > 0
            ORDER BY titulo ASC
            LIMIT ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listarComPaginacao(int $limite = 50, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_livros
            ORDER BY titulo ASC
            LIMIT ? OFFSET ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obterPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_livros
            WHERE id = ?
        ');
        $stmt->execute([$id]);
        $livro = $stmt->fetch(PDO::FETCH_ASSOC);
        return $livro ?: null;
    }

    public function buscarPorTitulo(string $titulo, int $limite = 50): array
    {
        $busca = '%' . $titulo . '%';
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_livros
            WHERE titulo LIKE ?
            ORDER BY titulo ASC
            LIMIT ?
        ');
        $stmt->bindValue(1, $busca);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function buscarPorAutor(string $autor, int $limite = 50): array
    {
        $busca = '%' . $autor . '%';
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_livros
            WHERE autor LIKE ?
            ORDER BY titulo ASC
            LIMIT ?
        ');
        $stmt->bindValue(1, $busca);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function contar(): int
    {
        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total
            FROM biblioteca_livros
        ');
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($resultado['total'] ?? 0);
    }

    public function contarDisponiveis(): int
    {
        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total
            FROM biblioteca_livros
            WHERE disponivel = 1 OR copias_disponiveis > 0
        ');
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($resultado['total'] ?? 0);
    }

    public function incrementarVisualizacoes(int $livroId): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE biblioteca_livros
            SET visualizacoes = visualizacoes + 1
            WHERE id = ?
        ');
        return $stmt->execute([$livroId]);
    }

    public function all(): array
    {
        return $this->listar();
    }

    public function findById(int $id): ?array
    {
        return $this->obterPorId($id);
    }

    public function create(array $dados): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO biblioteca_livros (titulo, autor, isbn, quantidade, quantidade_disponivel, criado_em)
            VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $dados['titulo'],
            $dados['autor'],
            $dados['isbn'],
            $dados['quantidade'] ?? 0,
            $dados['quantidade_disponivel'] ?? 0,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM biblioteca_livros WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function recordAudit(int $usuarioId, string $acao, ?array $meta = null): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO registro_auditoria (usuario_id, acao, meta, criado_em)
            VALUES (?, ?, ?, NOW())');
        $stmt->execute([
            $usuarioId,
            $acao,
            $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }
}
