<?php

class Comentario {
    private $autor;
    private $conteudo;

    public function __construct($autor, $conteudo) {
        $this->autor = $autor;
        $this->conteudo = $conteudo;
    }

    public function getConteudo() {
        return $this->conteudo;
    }

    public function getAutor() {
        return $this->autor->getNome();
    }
}