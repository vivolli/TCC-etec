<?php 
<<<<<<<< HEAD:app/Controllers/DadosPessoaisContato.php
// AVISO: Arquivo 'ClasseAbstrata.php' não foi encontrado. Comentado para evitar erro.
// require_once 'ClasseAbstrata.php';
========
require_once __DIR__ . '/ClasseAbstrata.php';
>>>>>>>> c20f15da362d705ddd5a772287b27b3ecff75f58:app/Views/FaleConosco/classes/DadosPessoais.php

class DadosPessoais extends FaleConosco {

    public function processar() {
        return "Solicitação de acesso a dados pessoais processada: " .
               $this->getMensagem() .
               " (Enviada por: " . $this->getNome() .
               ", Email: " . $this->getEmail() . ")<br>
               Entraremos em contato em breve.";
    }
}
