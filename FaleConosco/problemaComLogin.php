<?php 
class ProblemaComLogin extends FaleConosco {
    private $tipo = "Problema com Login";

    public function __construct($nome, $email, $mensagem) {
        parent::__construct($nome, $email, $mensagem);
    }

    public function processar() {
        echo "<p class=\"mensagem\">Obrigado por relatar seu problema de login, <strong>" . $this->getNome() . "</strong>! Nossa equipe entrará em contato em breve para ajudar a resolver o problema.</p>";
    }
}