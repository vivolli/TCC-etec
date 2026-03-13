<?php
// AVISO: Arquivo 'ClasseAbstrata.php' não foi encontrado. Comentado para evitar erro.
// require_once 'ClasseAbstrata.php';
class Duvida extends FaleConosco {

public function processar() {
    return "Dúvida processada: " . $this->getMensagem() . " (Enviada por: " . $this->getNome() . ", Email: " . $this->getEmail() . ")<br/>
    Entraremos em contato em breve para esclarecer sua dúvida.";
}
}

