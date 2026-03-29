<?php

namespace App\Models;

use PDO;

class Livro {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function buscar($filtros = []) {
        $titulo = $filtros['titulo'] ?? '';
        $autor = $filtros['autor'] ?? '';
        $categoria = $filtros['categoria'] ?? '';
        $ano = $filtros['ano'] ?? '';
        $disponibilidade = $filtros['disponibilidade'] ?? 'todos';
        $ordenacao = $filtros['ordenacao'] ?? 'titulo';
        $pagina = (int)($filtros['pagina'] ?? 1);
        $por_pagina = 20;
        $offset = ($pagina - 1) * $por_pagina;

        $sql = "SELECT * FROM biblioteca_livros WHERE 1=1";
        $params = [];

        if (!empty($titulo)) {
            $sql .= " AND titulo LIKE :titulo";
            $params['titulo'] = "%$titulo%";
        }

        if (!empty($autor)) {
            $sql .= " AND autor LIKE :autor";
            $params['autor'] = "%$autor%";
        }

        if (!empty($categoria)) {
            $sql .= " AND categoria = :categoria";
            $params['categoria'] = $categoria;
        }

        if (!empty($ano)) {
            $sql .= " AND ano_publicacao = :ano";
            $params['ano'] = (int)$ano;
        }

        if ($disponibilidade === 'disponiveis') {
            $sql .= " AND copias_disponiveis > 0";
        } elseif ($disponibilidade === 'indisponiveis') {
            $sql .= " AND copias_disponiveis = 0";
        }

        $sqlOrdenacao = match($ordenacao) {
            'data' => " ORDER BY ano_publicacao DESC",
            'autor' => " ORDER BY autor ASC",
            default => " ORDER BY titulo ASC"
        };

        $sqlCount = "SELECT COUNT(*) as total FROM biblioteca_livros WHERE 1=1";
        if (!empty($titulo)) $sqlCount .= " AND titulo LIKE :titulo";
        if (!empty($autor)) $sqlCount .= " AND autor LIKE :autor";
        if (!empty($categoria)) $sqlCount .= " AND categoria = :categoria";
        if (!empty($ano)) $sqlCount .= " AND ano_publicacao = :ano";
        if ($disponibilidade === 'disponiveis') $sqlCount .= " AND copias_disponiveis > 0";
        elseif ($disponibilidade === 'indisponiveis') $sqlCount .= " AND copias_disponiveis = 0";

        try {
            $stmtCount = $this->db->prepare($sqlCount);
            foreach ($params as $key => $value) {
                $stmtCount->bindValue(":$key", $value);
            }
            $stmtCount->execute();
            $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            $sql .= $sqlOrdenacao . " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Adicionar mídias (imagens e PDFs) para cada livro
            foreach ($livros as &$livro) {
                $livro['midias'] = $this->obterMidias($livro['id']);
            }

            return [
                'sucesso' => true,
                'dados' => $livros,
                'total' => $total,
                'pagina' => $pagina,
                'por_pagina' => $por_pagina,
                'total_paginas' => ceil($total / $por_pagina)
            ];
        } catch (\Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao buscar livros',
                'dados' => []
            ];
        }
    }

    public function obterMidias($livroId) {
        try {
            $stmt = $this->db->prepare("SELECT id, url_imagem, url_link, tipo FROM biblioteca_livros_midias WHERE livro_id = :livro_id");
            $stmt->bindValue(':livro_id', (int)$livroId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function obterPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM biblioteca_livros WHERE id = :id");
            $stmt->bindValue(':id', (int)$id);
            $stmt->execute();
            $livro = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($livro) {
                $livro['midias'] = $this->obterMidias($livro['id']);
            }
            
            return $livro;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function obterCategorias() {
        try {
            $stmt = $this->db->query("SELECT DISTINCT categoria FROM biblioteca_livros WHERE categoria IS NOT NULL ORDER BY categoria ASC");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function obterAnos() {
        try {
            $stmt = $this->db->query("SELECT DISTINCT ano_publicacao FROM biblioteca_livros WHERE ano_publicacao IS NOT NULL ORDER BY ano_publicacao DESC");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function atualizar($id, $dados) {
        try {
            $campos = [];
            $params = ['id' => $id];

            foreach ($dados as $campo => $valor) {
                $campos[] = "$campo = :$campo";
                $params[$campo] = $valor;
            }

            $sql = "UPDATE biblioteca_livros SET " . implode(", ", $campos) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            return $stmt->execute();
        } catch (\Exception $e) {
            return false;
        }
    }
}
?>
