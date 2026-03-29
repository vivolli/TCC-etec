<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\AuthService;
use App\Support\Auth;

class AuthController extends Controller
{
    private AuthService $authService;

    private const PROFILES = [
        'admin' => [
            'label' => 'Administradores',
            'descricao' => 'Área restrita a administradores autorizados.',
            'placeholder' => 'admin@fetel.edu.br',
            'botao' => 'Entrar (Administrador)',
        ],
        'aluno' => [
            'label' => 'Alunos',
            'descricao' => 'Use seu e-mail institucional para acessar o portal do aluno.',
            'placeholder' => 'aluno@fetel.edu.br',
            'botao' => 'Entrar no Portal do Aluno',
        ],
        'professor' => [
            'label' => 'Professores',
            'descricao' => 'Acesso para professores autorizados. Use seu e-mail institucional.',
            'placeholder' => 'professor@fetel.edu.br',
            'botao' => 'Entrar (Professor)',
        ],
        'secretaria' => [
            'label' => 'Secretaria',
            'descricao' => 'Acesso para equipe da secretaria e funcionários autorizados.',
            'placeholder' => 'secretaria@fetel.edu.br',
            'botao' => 'Entrar na Secretaria',
        ],
    ];

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showForm(): void
    {
        Auth::start();
        if (isset($_GET['logout'])) {
            Auth::logout();
            header('Location: /TCC-etec/login?success=' . urlencode('Você saiu com sucesso.'));
            exit;
        }

        $perfil = $this->normalizeProfile($_GET['perfil'] ?? null) ?? 'admin';
        $redirect = $this->authService->sanitizeRedirect($_GET['redirect'] ?? null, '/TCC-etec/');
        $erro = isset($_GET['error']) ? trim((string)$_GET['error']) : '';
        $mensagem = isset($_GET['success']) ? trim((string)$_GET['success']) : '';

        echo $this->view('auth/login', [
            'perfis' => self::PROFILES,
            'perfilAtivo' => $perfil,
            'erro' => $erro,
            'mensagem' => $mensagem,
            'redirect' => $redirect,
            'csrf' => Csrf::generateToken(),
        ]);
    }

    public function authenticate(): void
    {
        $token = $_POST['_csrf'] ?? null;
        if (!Csrf::validateToken($token)) {
            $this->redirectBack('Sessão expirada. Atualize a página e tente novamente.');
            return;
        }

        $perfil = $this->normalizeProfile($_POST['perfil'] ?? null);
        $redirectRequested = $_POST['redirect'] ?? '';
        $redirectSafe = $this->authService->sanitizeRedirect($redirectRequested, '/TCC-etec/');

        $fingerprint = AuthService::fingerprintFromGlobals();
        $result = $this->authService->attempt(
            (string)($_POST['email'] ?? ''),
            (string)($_POST['senha'] ?? ''),
            $perfil,
            $fingerprint
        );

        if (!$result['ok']) {
            $this->renderWithError($result['message'] ?? 'Não foi possível autenticar.', $perfil, $redirectSafe);
            return;
        }

        Auth::login($result['user']);

        $userModel = $this->authService->getUserModel();
        $userModel->recordAudit($result['user']['id'], 'login_realizado', $result['audit']);

        $target = $redirectSafe ?: $this->authService->defaultRedirectForRole($result['user']['papel'] ?? '');
        header('Location: ' . $target);
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /TCC-etec/login?success=' . urlencode('Você saiu com sucesso.'));
        exit;
    }

    private function redirectBack(string $message): void
    {
        header('Location: /TCC-etec/login?error=' . urlencode($message));
        exit;
    }

    private function renderWithError(string $message, ?string $perfil, string $redirect): void
    {
        $perfil = $perfil ?? 'admin';
        echo $this->view('auth/login', [
            'perfis' => self::PROFILES,
            'perfilAtivo' => $perfil,
            'erro' => $message,
            'mensagem' => '',
            'redirect' => $redirect,
            'csrf' => Csrf::generateToken(),
        ]);
    }

    private function normalizeProfile($perfil): ?string
    {
        if (!is_string($perfil)) {
            return null;
        }
        $perfil = strtolower(trim($perfil));
        return array_key_exists($perfil, self::PROFILES) ? $perfil : null;
    }
}
