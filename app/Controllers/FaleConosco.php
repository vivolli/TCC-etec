<?php

require_once "../Model/Duvida.php";
require_once "../Model/Feedback.php";
require_once "../Model/ProblemaLogin.php";
require_once "../Model/DadosPessoais.php";

class ContatoController {

    public function index() {

        $resultado = "";

        if ($_POST) {

            $tipo = $_POST['tipo'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $mensagem = $_POST['mensagem'];

            switch ($tipo) {

                case "duvida":
                    $contato = new Duvida($nome, $email, $mensagem);
                    break;

                case "reclamacao":
                    $contato = new Feedback($nome, $email, $mensagem, "Reclamação");
                    break;

                case "elogio":
                    $contato = new Feedback($nome, $email, $mensagem, "Elogio");
                    break;

                case "login":
                    $contato = new ProblemaLogin($nome, $email, $mensagem);
                    break;

                case "dados":
                    $contato = new DadosPessoais($nome, $email, $mensagem);
                    break;
            }

            $resultado = $contato->processar();
        }

        require "../views/contato/faleconosco.php";
    }

}