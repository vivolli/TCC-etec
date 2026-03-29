<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Domain\FaleConosco\Contato;
use App\Domain\FaleConosco\DadosPessoais;
use App\Domain\FaleConosco\Duvida;
use App\Domain\FaleConosco\Feedback;
use App\Domain\FaleConosco\ProblemaComLogin;

class FaleConoscoController extends Controller
{
    public function index(): void
    {
        $resultado = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrf'] ?? null;
            if (!Csrf::validateToken($token)) {
                $resultado = 'Requisição inválida (CSRF).';
            } else {
                $resultado = $this->processarContato(
                    (string)($_POST['tipo'] ?? ''),
                    trim((string)($_POST['nome'] ?? '')),
                    trim((string)($_POST['email'] ?? '')),
                    trim((string)($_POST['mensagem'] ?? ''))
                );
            }
        }

        $csrf = Csrf::generateToken();

        echo $this->view('FaleConosco/faleConosco', [
            'resultado' => $resultado,
            'csrf' => $csrf,
        ]);
    }

    private function processarContato(string $tipo, string $nome, string $email, string $mensagem): string
    {
        $contato = $this->criarContato($tipo, $nome, $email, $mensagem);
        if ($contato === null) {
            return 'Tipo de contato inválido.';
        }

        return $contato->processar();
    }

    private function criarContato(string $tipo, string $nome, string $email, string $mensagem): ?Contato
    {
        return match ($tipo) {
            'duvida' => new Duvida($nome, $email, $mensagem),
            'reclamacao' => new Feedback($nome, $email, $mensagem, 'Reclamação'),
            'elogio' => new Feedback($nome, $email, $mensagem, 'Elogio'),
            'login' => new ProblemaComLogin($nome, $email, $mensagem),
            'dados' => new DadosPessoais($nome, $email, $mensagem),
            default => null,
        };
    }
}
