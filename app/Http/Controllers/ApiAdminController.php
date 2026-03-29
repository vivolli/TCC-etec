<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Livro;
use App\Models\Noticia;
use App\Support\Auth;

class ApiAdminController extends Controller
{
    private User $usuarioModel;
    private Livro $livroModel;
    private Noticia $noticiaModel;

    public function __construct()
    {
        $this->usuarioModel = new User();
        $this->livroModel = new Livro();
        $this->noticiaModel = new Noticia();
    }

    public function listarUsuarios(): void
    {
        try {
            Auth::requireAuth();
            Auth::requireRole('admin');

            // CSRF token from header
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!\App\Core\Csrf::validateToken($token)) {
                $this->respondJson(['ok' => false, 'message' => 'Token CSRF inválido.'], 403);
            }

            $pagina = (int)($_GET['pagina'] ?? 1);
            $porPagina = (int)($_GET['por_pagina'] ?? 10);
            $offset = ($pagina - 1) * $porPagina;

            $usuarios = $this->usuarioModel->paginate($offset, $porPagina);
            $total = $this->usuarioModel->count();

            $this->respondJson([
                'data' => $usuarios,
                'pagina' => $pagina,
                'por_pagina' => $porPagina,
                'total' => $total,
            ]);
        } catch (\Throwable $e) {
            $this->respondJson(['ok' => false, 'message' => 'Erro ao listar usuários.'], 500);
        }
    }

    public function criarUsuario(): void
    {
        try {
            Auth::requireAuth();
            Auth::requireRole('admin');

            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!\App\Core\Csrf::validateToken($token)) {
                $this->respondJson(['ok' => false, 'message' => 'Token CSRF inválido.'], 403);
            }

            $dados = json_decode(file_get_contents('php://input'), true) ?? [];

            $email = filter_var((string)($dados['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $nome = trim((string)($dados['nome_completo'] ?? ''));
            $papel = trim((string)($dados['papel'] ?? ''));

            if (!$email || $nome === '' || $papel === '') {
                $this->respondJson(['ok' => false, 'message' => 'Campos obrigatórios faltando'], 400);
                return;
            }

            if ($this->usuarioModel->findByEmail($email)) {
                $this->respondJson(['ok' => false, 'message' => 'Email já existe'], 409);
                return;
            }

            $senha = $dados['senha'] ?? bin2hex(random_bytes(8));
            $usuarioId = $this->usuarioModel->create([
                'email' => $email,
                'nome_completo' => $nome,
                'papel' => $papel,
                'senha_hash' => User::hashPassword($senha),
                'ativo' => 1,
            ]);

            $this->usuarioModel->recordAudit(
                Auth::user()['id'],
                'usuario_criado',
                ['usuario_id' => $usuarioId, 'email' => $email]
            );

            $this->respondJson([
                'ok' => true,
                'data' => ['id' => $usuarioId, 'email' => $email],
            ], 201);
        } catch (\Throwable $e) {
            $this->respondJson(['ok' => false, 'message' => 'Erro ao criar usuário.'], 500);
        }
    }

    public function deletarUsuario(string $usuarioId): void
    {
        Auth::requireAuth();
        Auth::requireRole('admin');

        $id = (int)$usuarioId;
        if ($id <= 0) {
            $this->respondJson(['ok' => false, 'message' => 'ID inválido'], 400);
            return;
        }

        $usuario = $this->usuarioModel->findById($id);
        if (!$usuario) {
            $this->respondJson(['ok' => false, 'message' => 'Usuário não encontrado'], 404);
            return;
        }

        $this->usuarioModel->delete($id);
        $this->usuarioModel->recordAudit(
            Auth::user()['id'],
            'usuario_deletado',
            ['usuario_id' => $id, 'email' => $usuario['email']]
        );

        $this->respondJson(['ok' => true], 200);
    }

    public function listarLivros(): void
    {
        try {
            Auth::requireAuth();
            Auth::requireRole('admin');

            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!\App\Core\Csrf::validateToken($token)) {
                $this->respondJson(['ok' => false, 'message' => 'Token CSRF inválido.'], 403);
            }

            $livros = $this->livroModel->all();
            $this->respondJson(['data' => $livros]);
        } catch (\Throwable $e) {
            $this->respondJson(['ok' => false, 'message' => 'Erro ao listar livros.'], 500);
        }
    }

    public function criarLivro(): void
    {
        try {
            Auth::requireAuth();
            Auth::requireRole('admin');

            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!\App\Core\Csrf::validateToken($token)) {
                $this->respondJson(['ok' => false, 'message' => 'Token CSRF inválido.'], 403);
            }

            $dados = json_decode(file_get_contents('php://input'), true) ?? [];

            $titulo = trim((string)($dados['titulo'] ?? ''));
            $autor = trim((string)($dados['autor'] ?? ''));
            $isbn = trim((string)($dados['isbn'] ?? ''));

            if ($titulo === '' || $autor === '' || $isbn === '') {
                $this->respondJson(['ok' => false, 'message' => 'Campos obrigatórios faltando'], 400);
                return;
            }

            $livroId = $this->livroModel->create([
                'titulo' => $titulo,
                'autor' => $autor,
                'isbn' => $isbn,
                'quantidade' => (int)($dados['quantidade'] ?? 0),
                'quantidade_disponivel' => (int)($dados['quantidade'] ?? 0),
            ]);

            $this->livroModel->recordAudit(
                Auth::user()['id'],
                'livro_criado',
                ['livro_id' => $livroId, 'titulo' => $titulo]
            );

            $this->respondJson(['ok' => true, 'data' => ['id' => $livroId]], 201);
        } catch (\Throwable $e) {
            $this->respondJson(['ok' => false, 'message' => 'Erro ao criar livro.'], 500);
        }
    }

    public function deletarLivro(string $livroId): void
    {
        Auth::requireAuth();
        Auth::requireRole('admin');

        $id = (int)$livroId;
        $livro = $this->livroModel->findById($id);
        if (!$livro) {
            $this->respondJson(['ok' => false, 'message' => 'Livro não encontrado'], 404);
            return;
        }

        $this->livroModel->delete($id);
        $this->livroModel->recordAudit(
            Auth::user()['id'],
            'livro_deletado',
            ['livro_id' => $id, 'titulo' => $livro['titulo']]
        );

        $this->respondJson(['ok' => true], 200);
    }

    public function listarNoticias(): void
    {
        Auth::requireAuth();
        Auth::requireRole('admin');

        $noticias = $this->noticiaModel->all();
        $this->respondJson(['data' => $noticias]);
    }

    public function criarNoticia(): void
    {
        Auth::requireAuth();
        Auth::requireRole('admin');

        $dados = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($dados['titulo']) || empty($dados['conteudo'])) {
            $this->respondJson(['ok' => false, 'message' => 'Título e conteúdo são obrigatórios'], 400);
            return;
        }

        $usuario = Auth::user();
        $noticiaId = $this->noticiaModel->create([
            'titulo' => $dados['titulo'],
            'conteudo' => $dados['conteudo'],
            'autor_id' => $usuario['id'],
            'publicado' => (int)($dados['publicado'] ?? 0),
        ]);

        $this->noticiaModel->recordAudit(
            $usuario['id'],
            'noticia_criada',
            ['noticia_id' => $noticiaId, 'titulo' => $dados['titulo']]
        );

        $this->respondJson(['ok' => true, 'data' => ['id' => $noticiaId]], 201);
    }

    public function deletarNoticia(string $noticiaId): void
    {
        Auth::requireAuth();
        Auth::requireRole('admin');

        $id = (int)$noticiaId;
        $noticia = $this->noticiaModel->findById($id);
        if (!$noticia) {
            $this->respondJson(['ok' => false, 'message' => 'Notícia não encontrada'], 404);
            return;
        }

        $this->noticiaModel->delete($id);
        $this->noticiaModel->recordAudit(
            Auth::user()['id'],
            'noticia_deletada',
            ['noticia_id' => $id, 'titulo' => $noticia['titulo']]
        );

        $this->respondJson(['ok' => true], 200);
    }

    public function auditoria(): void
    {
        Auth::requireAuth();
        Auth::requireRole('admin');

        $limite = (int)($_GET['limite'] ?? 50);
        $registros = $this->usuarioModel->fetchAuditLog($limite);

        $this->respondJson(['data' => $registros]);
    }

    private function respondJson(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
