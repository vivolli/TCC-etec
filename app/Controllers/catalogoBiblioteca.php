<?php

require_once __DIR__ . '/../Model/Livro.php';
require_once __DIR__ . '/../../Biblioteca/_acesso.php';

class CatalogoController {

    public function index() {

        requer_aluno();
        $info = get_aluno_info();

        $books = Livro::listar();

        if (empty($books)) {

            $books = [
                ['id'=>1,'titulo'=>'Algoritmos e Lógica','autor'=>'Maria Silva','ano'=>'2019','disponivel'=>1],
                ['id'=>2,'titulo'=>'Introdução ao PHP','autor'=>'João Souza','ano'=>'2020','disponivel'=>1],
                ['id'=>3,'titulo'=>'Redes e Infraestrutura','autor'=>'Carlos Pereira','ano'=>'2018','disponivel'=>0],
            ];

        }

        require __DIR__ . '/../views/biblioteca/catalogo.php';

    }

}