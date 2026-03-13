<?php

class Emprestimo {

    public static function solicitar($usuario_id, $livroId, $pdo) {

        $stmt = $pdo->prepare(
            "INSERT INTO emprestimos 
            (usuario_id, livro_id, data_solicitacao, status) 
            VALUES (:uid, :lid, NOW(), :st)"
        );

        return $stmt->execute([
            ':uid' => $usuario_id,
            ':lid' => $livroId,
            ':st' => 'solicitado'
        ]);
    }

    public static function listar($usuario_id, $pdo) {

        $q = $pdo->prepare(
            "SELECT e.id, e.livro_id, l.titulo, e.data_solicitacao, e.status
            FROM emprestimos e
            LEFT JOIN livros l ON l.id = e.livro_id
            WHERE e.usuario_id = :uid
            ORDER BY e.data_solicitacao DESC"
        );

        $q->execute([':uid' => $usuario_id]);

        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

}