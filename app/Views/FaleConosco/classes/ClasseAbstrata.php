<?php
abstract class FaleConosco {
    protected $nome;
    protected $email;
    protected $mensagem;

    public function __construct($nome, $email, $mensagem) {
        $this->nome = $nome;
        $this->email = $email;
        $this->mensagem = $mensagem;
    }
    
    abstract public function processar();


    public function getNome() {
        return $this->nome;
    }


    public function getMensagem() {
        return $this->mensagem;
    }

    public function getEmail() {
        return $this->email;
    }
}
