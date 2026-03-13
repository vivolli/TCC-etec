<?php
namespace App\Controllers;

use App\Core\SessionManager;
use App\Services\AuthService;
use App\Core; // for app_get_pdo

class AuthController
{
    private AuthService $auth;

    public function __construct()
    {
        $pdo = null;
        if (function_exists('\App\Core\app_get_pdo')) {
            $pdo = \App\Core\app_get_pdo();
        }
        if (!$pdo) {
            // try legacy getPDO
            if (function_exists('getPDO')) $pdo = getPDO();
        }
        if (!$pdo) throw new \RuntimeException('Banco de dados não disponível');

        $this->auth = new AuthService($pdo);
    }

    public function handleAlunoLogin(array $post, array $get): void
    {
        $session = SessionManager::getInstance();
        $session->start();

        // If already logged-in and role is aluno, redirect
        $info = $session->getSessionInfo();
        $papel = strtolower((string)($info['papel'] ?? ''));
        if (!empty($info['logado']) && in_array($papel, ['aluno','estudante','student'], true)) {
            $redirect = $post['redirect'] ?? $get['redirect'] ?? null;
            if ($redirect && strpos($redirect, '/') === 0 && strpos($redirect, '//') !== 0) {
                header('Location: ' . $redirect);
            } else {
                header('Location: /TCC-etec/');
            }
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/app/php/login/alunos/login.html');
            exit;
        }

        $email = filter_var($post['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $senha = $post['senha'] ?? '';

        if (!$email || $senha === '') {
            header('Location: /TCC-etec/app/php/login/alunos/login.html?error=Dados inválidos');
            exit;
        }

        $result = $this->auth->attemptLogin($email, $senha, 'aluno');
        if ($result['ok']) {
            $redirect = $post['redirect'] ?? $get['redirect'] ?? null;
            if ($redirect && strpos($redirect, '/') === 0 && strpos($redirect, '//') !== 0) {
                header('Location: ' . $redirect);
            } else {
                header('Location: /TCC-etec/');
            }
            exit;
        }

        header('Location: /TCC-etec/app/php/login/alunos/login.html?error=' . urlencode($result['error']));
        exit;
    }
}


