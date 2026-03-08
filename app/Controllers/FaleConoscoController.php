<?php
// Controller for FaleConosco: handles POST, instantiates classes and renders view
class FaleConoscoController {
    public static function handle(): void {
        // include minimal classes (they live under app/Views/FaleConosco/classes)
        require_once __DIR__ . '/../Views/FaleConosco/classes/ClasseAbstrata.php';
        require_once __DIR__ . '/../Views/FaleConosco/classes/duvidasFrequentes.php';
        require_once __DIR__ . '/../Views/FaleConosco/classes/reclamacoes.php';
        require_once __DIR__ . '/../Views/FaleConosco/classes/problemaComLogin.php';
        require_once __DIR__ . '/../Views/FaleConosco/classes/DadosPessoais.php';

        $resultado = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'] ?? '';
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $mensagem = $_POST['mensagem'] ?? '';

            switch ($tipo) {
                case 'duvida':
                    $contato = new Duvida($nome, $email, $mensagem);
                    break;
                case 'reclamacao':
                    $contato = new Feedback($nome, $email, $mensagem, 'Reclamação');
                    break;
                case 'elogio':
                    $contato = new Feedback($nome, $email, $mensagem, 'Elogio');
                    break;
                case 'login':
                    $contato = new ProblemaComLogin($nome, $email, $mensagem);
                    break;
                case 'dados':
                    $contato = new DadosPessoais($nome, $email, $mensagem);
                    break;
                default:
                    $contato = null;
            }

            if ($contato !== null) {
                $resultado = $contato->processar();
            }
        }

        // expose $resultado to the view expected by the template
        // the view file uses $resultado variable
        include __DIR__ . '/../Views/FaleConosco/faleConosco.php';
    }
}
