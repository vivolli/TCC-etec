<?php

namespace App\Domain\FaleConosco;

class Feedback extends Contato
{
    public function __construct(string $nome, string $email, string $mensagem, private string $tipo)
    {
        parent::__construct($nome, $email, $mensagem);
    }

    public function processar(): string
    {
        return sprintf(
            'Obrigado por seu feedback, %s! Tipo: %s<br>Mensagem: %s',
            $this->getNome(),
            $this->tipo,
            $this->getMensagem()
        );
    }
}
