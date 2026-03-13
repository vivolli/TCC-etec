<?php

class BibliotecaController {

    public function index($info) {

        $nome = htmlspecialchars($info['nome'] ?? 'Aluno');

        require __DIR__ . '/../View/biblioteca_home.php';
    }

}