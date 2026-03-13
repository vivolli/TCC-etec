<?php
<<<<<<<< HEAD:app/Controllers/duvidasFrequentesContatos.php
// AVISO: Arquivo 'ClasseAbstrata.php' não foi encontrado. Comentado para evitar erro.
// require_once 'ClasseAbstrata.php';
========
require_once __DIR__ . '/ClasseAbstrata.php';
>>>>>>>> c20f15da362d705ddd5a772287b27b3ecff75f58:app/Views/FaleConosco/classes/duvidasFrequentes.php
class Duvida extends FaleConosco {

public function processar() {
    return "Dúvida processada: " . $this->getMensagem() . " (Enviada por: " . $this->getNome() . ", Email: " . $this->getEmail() . ")<br/>
    Entraremos em contato em breve para esclarecer sua dúvida.";
}
}
