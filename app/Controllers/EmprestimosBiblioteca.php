<?php

require_once __DIR__ . '/../Model/Emprestimos.php';
require_once __DIR__ . '/../../Config/db/conexao.php';

class EmprestimoController {

    public function index($usuario_id) {

        $pdo = getPDO();
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_id'])) {

            $livroId = intval($_POST['livro_id']);
            $csrf = $_POST['csrf'] ?? '';

            if (!validate_csrf($csrf)) {

                $message = "Token inválido";

            } else {

                if (Emprestimo::solicitar($usuario_id, $livroId, $pdo)) {
                    $message = "Empréstimo solicitado com sucesso.";
                } else {
                    $message = "Erro ao solicitar empréstimo.";
                }

            }

        }

        $loans = Emprestimo::listar($usuario_id, $pdo);

        require __DIR__ . '/../Views/emprestimos.php';

    }

}