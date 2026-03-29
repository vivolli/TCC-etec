<?php

namespace App\Core;

class Application
{
    private Router $router;

    public function __construct()
    {
        Bootstrap::init();
        $this->router = new Router();
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        
        $basePath = '/TCC-etec/public';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath)) ?: '/';
        } else {
            $prefix = '/TCC-etec';
            if (strpos($path, $prefix) === 0) {
                $path = substr($path, strlen($prefix)) ?: '/';
            }
        }

        // Proteção física de rotas admin: bloquear acesso se não for admin
        if (str_starts_with($path, '/admin') || str_starts_with($path, '/api/admin') || str_starts_with($path, '/api/usuarios') || str_starts_with($path, '/api/livros') || str_starts_with($path, '/api/noticias')) {
            try {
                \App\Support\Auth::start();
                if (!\App\Support\Auth::check() || strtolower(\App\Support\Auth::role()) !== 'admin') {
                    // se for requisição API, retornar JSON
                    if (str_starts_with($path, '/api')) {
                        http_response_code(403);
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(['ok' => false, 'message' => 'Acesso negado - admin requerido']);
                        return;
                    }
                    header('Location: /TCC-etec/login?error=' . urlencode('Acesso negado. Faça login como administrador.'));
                    return;
                }
            } catch (\Throwable $e) {
                // não vaza detalhes
                header('Location: /TCC-etec/login?error=' . urlencode('Autenticação necessária.'));
                return;
            }
        }

        if ($this->router->dispatch($method, $path)) {
            return;
        }

        $this->serveFrontend();
    }

    private function serveFrontend(): void
    {
        echo \App\Support\Frontend::renderLanding();
    }
}
