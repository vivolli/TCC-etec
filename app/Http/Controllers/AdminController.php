<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\Livro;
use App\Models\Noticia;
use App\Models\Funcionario;
use App\Support\Auth;

class AdminController extends Controller
{
    private Livro $livroModel;
    private Noticia $noticiaModel;
    private Funcionario $funcionarioModel;

    public function __construct()
    {
        $this->livroModel = new Livro();
        $this->noticiaModel = new Noticia();
        $this->funcionarioModel = new Funcionario();
    }

    public function painel(): void
    {
        Auth::start();
        Auth::requireRole('admin');

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];
        $usuarioNome = $usuario['nome'];

        try {
            $funcionario = $this->funcionarioModel->obterCompleto($usuarioId);

            if (!$funcionario) {
                header('Location: /TCC-etec/login?error=' . urlencode('Acesso administrativo não autorizado.'));
                exit;
            }

            $totalLivros = $this->livroModel->contar();
            $totalNoticias = $this->noticiaModel->contar();
            $solicitacoesPendentes = $this->funcionarioModel->buscarSolicitacoesPorStatus('pendente');
            $estatisticas = $this->funcionarioModel->obterEstatisticasBiblioteca();

            $contadores = [
                'total_livros' => $totalLivros,
                'total_noticias' => $totalNoticias,
                'solicitacoes_pendentes' => count($solicitacoesPendentes),
                'emprestimos_atrasados' => count($this->funcionarioModel->buscarEmprestimosAtrasados())
            ];

            echo $this->view('admin/painel', [
                'usuario_nome' => $usuarioNome,
                'funcionario' => $funcionario,
                'contadores' => $contadores,
                'estatisticas' => $estatisticas
            ]);
        } catch (\Throwable $e) {
            error_log('Erro no painel administrativo: ' . $e->getMessage());
            header('Location: /TCC-etec/login?error=' . urlencode('Erro ao carregar painel.'));
            exit;
        }
    }

    public function gerenciarLivros(): void
    {
        Auth::start();
        Auth::requireRole('admin');

        try {
            $pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limite = 25;
            $offset = ($pagina - 1) * $limite;

            $livros = $this->livroModel->listarComPaginacao($limite, $offset);
            $totalLivros = $this->livroModel->contar();
            $totalPaginas = ceil($totalLivros / $limite);

            echo $this->view('admin/livros', [
                'livros' => $livros,
                'pagina_atual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_livros' => $totalLivros
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao listar livros: ' . $e->getMessage());
            header('Location: /TCC-etec/admin?error=' . urlencode('Erro ao carregar livros.'));
            exit;
        }
    }

    public function criarLivro(): void
    {
        Auth::start();
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/admin/livros');
            exit;
        }

        $titulo = isset($_POST['titulo']) ? trim((string)$_POST['titulo']) : '';
        $autor = isset($_POST['autor']) ? trim((string)$_POST['autor']) : '';
        $editora = isset($_POST['editora']) ? trim((string)$_POST['editora']) : '';
        $isbn = isset($_POST['isbn']) ? trim((string)$_POST['isbn']) : '';
        $copias = isset($_POST['copias']) ? (int)$_POST['copias'] : 0;

        if (empty($titulo) || empty($autor) || $copias <= 0) {
            header('Location: /TCC-etec/admin/livros?error=' . urlencode('Preencha os campos obrigatórios.'));
            exit;
        }

        try {
            $stmt = $GLOBALS['pdo']->prepare('
                INSERT INTO biblioteca_livros
                (titulo, autor, editora, isbn, copias_totais, copias_disponiveis, disponivel, criado_em)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ');

            $disponivel = $copias > 0 ? 1 : 0;

            if ($stmt->execute([$titulo, $autor, $editora, $isbn, $copias, $copias, $disponivel])) {
                header('Location: /TCC-etec/admin/livros?success=' . urlencode('Livro criado com sucesso.'));
            } else {
                header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao criar livro.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao criar livro: ' . $e->getMessage());
            header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao processar criação.'));
        }
        exit;
    }

    public function editarLivro(int $livroId): void
    {
        Auth::start();
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/admin/livros');
            exit;
        }

        try {
            $livro = $this->livroModel->obterPorId($livroId);

            if (!$livro) {
                header('Location: /TCC-etec/admin/livros?error=' . urlencode('Livro não encontrado.'));
                exit;
            }

            $titulo = isset($_POST['titulo']) ? trim((string)$_POST['titulo']) : $livro['titulo'];
            $autor = isset($_POST['autor']) ? trim((string)$_POST['autor']) : $livro['autor'];
            $editora = isset($_POST['editora']) ? trim((string)$_POST['editora']) : $livro['editora'];
            $copias = isset($_POST['copias']) ? (int)$_POST['copias'] : $livro['copias_totais'];

            $stmt = $GLOBALS['pdo']->prepare('
                UPDATE biblioteca_livros
                SET titulo = ?, autor = ?, editora = ?, copias_totais = ?, disponivel = ?
                WHERE id = ?
            ');

            $disponivel = $copias > 0 ? 1 : 0;

            if ($stmt->execute([$titulo, $autor, $editora, $copias, $disponivel, $livroId])) {
                header('Location: /TCC-etec/admin/livros?success=' . urlencode('Livro atualizado.'));
            } else {
                header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao atualizar livro.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao editar livro: ' . $e->getMessage());
            header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao processar edição.'));
        }
        exit;
    }

    public function deletarLivro(int $livroId): void
    {
        Auth::start();
        Auth::requireRole('admin');

        try {
            $livro = $this->livroModel->obterPorId($livroId);

            if (!$livro) {
                header('Location: /TCC-etec/admin/livros?error=' . urlencode('Livro não encontrado.'));
                exit;
            }

            $stmt = $GLOBALS['pdo']->prepare('DELETE FROM biblioteca_livros WHERE id = ?');

            if ($stmt->execute([$livroId])) {
                header('Location: /TCC-etec/admin/livros?success=' . urlencode('Livro removido.'));
            } else {
                header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao remover livro.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao deletar livro: ' . $e->getMessage());
            header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao processar exclusão.'));
        }
        exit;
    }

    public function gerenciarNoticias(): void
    {
        Auth::start();
        Auth::requireRole('admin');

        try {
            $pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limite = 20;
            $offset = ($pagina - 1) * $limite;

            $noticias = $this->noticiaModel->listar($limite, $offset);
            $totalNoticias = $this->noticiaModel->contar();
            $totalPaginas = ceil($totalNoticias / $limite);

            echo $this->view('admin/noticias', [
                'noticias' => $noticias,
                'pagina_atual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_noticias' => $totalNoticias
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao listar noticias: ' . $e->getMessage());
            header('Location: /TCC-etec/admin?error=' . urlencode('Erro ao carregar noticias.'));
            exit;
        }
    }

    public function criarNoticia(): void
    {
        Auth::start();
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/admin/noticias');
            exit;
        }

        $titulo = isset($_POST['titulo']) ? trim((string)$_POST['titulo']) : '';
        $conteudo = isset($_POST['conteudo']) ? trim((string)$_POST['conteudo']) : '';
        $perfil_destino = isset($_POST['perfil_destino']) ? trim((string)$_POST['perfil_destino']) : 'aluno';
        $publicada = isset($_POST['publicada']) ? 1 : 0;

        if (empty($titulo) || empty($conteudo)) {
            header('Location: /TCC-etec/admin/noticias?error=' . urlencode('Preencha os campos obrigatórios.'));
            exit;
        }

        try {
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $titulo), '-'));

            if ($this->noticiaModel->criar([
                'titulo' => $titulo,
                'slug' => $slug,
                'conteudo' => $conteudo,
                'perfil_destino' => $perfil_destino,
                'publicada' => $publicada
            ])) {
                header('Location: /TCC-etec/admin/noticias?success=' . urlencode('Noticia criada com sucesso.'));
            } else {
                header('Location: /TCC-etec/admin/noticias?error=' . urlencode('Erro ao criar noticia.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao criar noticia: ' . $e->getMessage());
            header('Location: /TCC-etec/admin/noticias?error=' . urlencode('Erro ao processar criação.'));
        }
        exit;
    }

    public function editarNoticia(int $noticiaId): void
    {
        Auth::start();
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/admin/noticias');
            exit;
        }

        try {
            $noticia = $this->noticiaModel->obterPorId($noticiaId);

            if (!$noticia) {
                header('Location: /TCC-etec/admin/noticias?error=' . urlencode('Noticia não encontrada.'));
                exit;
            }

            $titulo = isset($_POST['titulo']) ? trim((string)$_POST['titulo']) : $noticia['titulo'];
            $conteudo = isset($_POST['conteudo']) ? trim((string)$_POST['conteudo']) : $noticia['conteudo'];
            $perfil_destino = isset($_POST['perfil_destino']) ? trim((string)$_POST['perfil_destino']) : $noticia['perfil_destino'];
            $publicada = isset($_POST['publicada']) ? 1 : 0;
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $titulo), '-'));

            if ($this->noticiaModel->atualizar($noticiaId, [
                'titulo' => $titulo,
                'slug' => $slug,
                'conteudo' => $conteudo,
                'perfil_destino' => $perfil_destino,
                'publicada' => $publicada
            ])) {
                header('Location: /TCC-etec/admin/noticias?success=' . urlencode('Noticia atualizada.'));
            } else {
                header('Location: /TCC-etec/admin/noticias?error=' . urlencode('Erro ao atualizar noticia.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao editar noticia: ' . $e->getMessage());
            header('Location: /TCC-etec/admin/noticias?error=' . urlencode('Erro ao processar edição.'));
        }
        exit;
    }

    public function deletarNoticia(int $noticiaId): void
    {
        Auth::start();
        Auth::requireRole('admin');

        try {
            $noticia = $this->noticiaModel->obterPorId($noticiaId);

            if (!$noticia) {
                header('Location: /TCC-etec/admin/noticias?error=' . urlencode('Noticia não encontrada.'));
                exit;
            }

            if ($this->noticiaModel->deletar($noticiaId)) {
                header('Location: /TCC-etec/admin/noticias?success=' . urlencode('Noticia removida.'));
            } else {
                header('Location: /TCC-etec/admin/noticias?error=' . urlencode('Erro ao remover noticia.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao deletar noticia: ' . $e->getMessage());
            header('Location: /TCC-etec/admin/noticias?error=' . urlencode('Erro ao processar exclusão.'));
        }
        exit;
    }
}
