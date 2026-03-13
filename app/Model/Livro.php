<?php

require_once __DIR__ . '/../../Config/db/conexao.php';

class Livro {

    public static function listar() {

        try {

            if (function_exists('getPDO')) {

                $pdo = getPDO();

                $q = $pdo->prepare(
                    "SELECT id, titulo, autor, ano, disponivel
                     FROM livros
                     ORDER BY titulo
                     LIMIT 200"
                );

                $q->execute();

                return $q->fetchAll(PDO::FETCH_ASSOC);

            }

        } catch (Throwable $e) {}

        return [];
    }

}