<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\Livro;
use App\Models\Emprestimo;
use App\Support\Auth;

class BibliotecaController extends Controller
{
    private Livro $livroModel;
    private Emprestimo $emprestimoModel;

    public function __construct()
    {
        $this->livroModel = new Livro();
        $this->emprestimoModel = new Emprestimo();
    }

    public function index(): void
    {
        Auth::start();

        $pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $busca = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $limite = 20;
        $offset = ($pagina - 1) * $limite;

        try {
            if (!empty($busca)) {
                $livros = $this->livroModel->buscarPorTitulo($busca, 100);
                $totalLivros = count($livros);
                $livros = array_slice($livros, $offset, $limite);
            } else {
                $livros = $this->livroModel->listarComPaginacao($limite, $offset);
                $totalLivros = $this->livroModel->contar();
            }

            $totalPaginas = ceil($totalLivros / $limite);

            $emprestimosAtivos = [];
            if (Auth::check()) {
                $usuario = Auth::user();
                $emprestimosAtivos = $this->emprestimoModel->buscarAtivos($usuario['id']);
            }

            echo $this->view('biblioteca/index', [
                'livros' => $livros,
                'total_livros' => $totalLivros,
                'pagina_atual' => $pagina,
                'total_paginas' => $totalPaginas,
                'busca' => $busca,
                'emprestimos_ativos' => $emprestimosAtivos,
                'autenticado' => Auth::check()
            ]);
        } catch (\Throwable $e) {
            error_log('Erro na biblioteca: ' . $e->getMessage());
            echo $this->view('biblioteca/index', [
                'livros' => [],
                'total_livros' => 0,
                'pagina_atual' => 1,
                'total_paginas' => 0,
                'busca' => $busca,
                'emprestimos_ativos' => [],
                'autenticado' => Auth::check(),
                'erro' => 'Erro ao carregar livros'
            ]);
        }
    }

    public function catalogo(): void
    {
        Auth::start();

        try {
            $limite = 50;
            $offset = 0;
            $livros = $this->livroModel->listarComPaginacao($limite, $offset);

            echo $this->view('biblioteca/catalogo', [
                'livros' => $livros,
                'autenticado' => Auth::check()
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao carregar catálogo: ' . $e->getMessage());
            header('Location: /TCC-etec/?error=' . urlencode('Erro ao carregar catálogo.'));
            exit;
        }
    }

    public function mostrar(int $livroId): void
    {
        Auth::start();

        try {
            $livro = $this->livroModel->obterPorId($livroId);

            if (!$livro) {
                header('HTTP/1.1 404 Not Found');
                echo $this->view('erros/404');
                exit;
            }

            $this->livroModel->incrementarVisualizacoes($livroId);

            $jaEmprestado = false;
            if (Auth::check()) {
                $usuario = Auth::user();
                $emprestimosAtivos = $this->emprestimoModel->buscarAtivos($usuario['id']);
                $jaEmprestado = in_array($livroId, array_column($emprestimosAtivos, 'livro_id'));
            }

            echo $this->view('biblioteca/livro', [
                'livro' => $livro,
                'ja_emprestado' => $jaEmprestado,
                'autenticado' => Auth::check()
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao carregar livro: ' . $e->getMessage());
            header('Location: /TCC-etec/biblioteca?error=' . urlencode('Erro ao carregar livro.'));
            exit;
        }
    }

    public function buscar(): void
    {
        Auth::start();

        $termo = isset($_GET['q']) ? trim((string)$_GET['q']) : '';

        if (empty($termo)) {
            header('Location: /TCC-etec/biblioteca');
            exit;
        }

        try {
            $livrosPorTitulo = $this->livroModel->buscarPorTitulo($termo, 50);
            $livrosPorAutor = $this->livroModel->buscarPorAutor($termo, 50);

            $livros = array_merge($livrosPorTitulo, $livrosPorAutor);
            $livros = array_values(array_unique($livros, SORT_REGULAR));

            $emprestimosAtivos = [];
            if (Auth::check()) {
                $usuario = Auth::user();
                $emprestimosAtivos = $this->emprestimoModel->buscarAtivos($usuario['id']);
            }

            echo $this->view('biblioteca/resultados', [
                'livros' => $livros,
                'termo' => $termo,
                'total' => count($livros),
                'emprestimos_ativos' => $emprestimosAtivos,
                'autenticado' => Auth::check()
            ]);
        } catch (\Throwable $e) {
            error_log('Erro na busca: ' . $e->getMessage());
            header('Location: /TCC-etec/biblioteca?error=' . urlencode('Erro ao buscar livros.'));
            exit;
        }
    }
}
