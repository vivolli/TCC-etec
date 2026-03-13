<?php
<<<<<<<< HEAD:app/Controllers/reclamacoes.php
// AVISO: Arquivo 'ClasseAbstrata.php' não foi encontrado. Comentado para evitar erro.
// require_once 'ClasseAbstrata.php';
========
require_once __DIR__ . '/ClasseAbstrata.php';
>>>>>>>> c20f15da362d705ddd5a772287b27b3ecff75f58:app/Views/FaleConosco/classes/reclamacoes.php

class Feedback extends FaleConosco {

    private $tipo;

    public function __construct($nome, $email, $mensagem, $tipo) {
        parent::__construct($nome, $email, $mensagem);
        $this->tipo = $tipo;
    }

    public function processar() {
        return "Obrigado por seu feedback, " . $this->getNome() .
               "! Tipo: " . $this->tipo .
               "<br>Mensagem: " . $this->getMensagem();
    }
}
