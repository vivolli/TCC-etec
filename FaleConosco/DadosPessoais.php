<?php 
require_once 'ClasseAbstrata.php';

class DadosPessoais extends FaleConosco {

    public function processar() {
        return "Solicitação de acesso a dados pessoais processada: " .
               $this->getMensagem() .
               " (Enviada por: " . $this->getNome() .
               ", Email: " . $this->getEmail() . ")<br>
               Entraremos em contato em breve.";
    }
}