<?php

use App\Core\Application;

$app = new Application();
$router = $app->router();

$router->get('/login', 'AuthController@showForm');
$router->post('/login', 'AuthController@authenticate');
$router->get('/admin', 'AdminPageController@__invoke');

$router->get('/aluno', 'DashboardAlunoController@index');
$router->get('/aluno/biblioteca', 'DashboardAlunoController@biblioteca');
$router->post('/aluno/emprestimo/solicitar', 'DashboardAlunoController@solicitarEmprestimo');
$router->get('/aluno/perfil', 'DashboardAlunoController@meusPerfis');
$router->get('/aluno/solicitacoes', 'DashboardAlunoController@minhasSolicitacoes');
$router->post('/aluno/solicitacao/criar', 'DashboardAlunoController@criarSolicitacao');

$router->get('/biblioteca', 'BibliotecaController@index');
$router->get('/biblioteca/catalogo', 'BibliotecaController@catalogo');
$router->get('/biblioteca/livro/{id}', 'BibliotecaController@mostrar');
$router->get('/biblioteca/buscar', 'BibliotecaController@buscar');

$router->get('/emprestimos', 'EmprestimoController@index');
$router->post('/emprestimo/criar', 'EmprestimoController@criar');
$router->post('/emprestimo/renovar', 'EmprestimoController@renovar');
$router->get('/emprestimo/{id}', 'EmprestimoController@detalhes');

$router->get('/admin', 'AdminPageController@__invoke');
$router->get('/admin/livros', 'AdminController@gerenciarLivros');
$router->post('/admin/livro/criar', 'AdminController@criarLivro');
$router->post('/admin/livro/{id}/editar', 'AdminController@editarLivro');
$router->post('/admin/livro/{id}/deletar', 'AdminController@deletarLivro');
$router->get('/admin/noticias', 'AdminController@gerenciarNoticias');
$router->post('/admin/noticia/criar', 'AdminController@criarNoticia');
$router->post('/admin/noticia/{id}/editar', 'AdminController@editarNoticia');
$router->post('/admin/noticia/{id}/deletar', 'AdminController@deletarNoticia');

$router->get('/secretaria', 'SecretariaController@painel');
$router->get('/secretaria/solicitacoes', 'SecretariaController@gerenciarSolicitacoes');
$router->post('/secretaria/solicitacao/{id}/atualizar', 'SecretariaController@atualizarSolicitacao');
$router->get('/secretaria/emprestimos', 'SecretariaController@gerenciarEmprestimos');
$router->post('/secretaria/emprestimo/{id}/devolver', 'SecretariaController@registrarDevolucao');
$router->get('/secretaria/alunos', 'SecretariaController@gerenciarAlunos');
$router->get('/secretaria/aluno/{id}', 'SecretariaController@visualizarAluno');
$router->get('/secretaria/auditoria', 'SecretariaController@auditoria');
$router->get('/secretaria/relatorios', 'SecretariaController@relatorios');

$router->get('/api/usuarios', 'ApiAdminController@listarUsuarios');
$router->post('/api/usuarios', 'ApiAdminController@criarUsuario');
$router->post('/api/usuarios/{usuarioId}/deletar', 'ApiAdminController@deletarUsuario');

$router->get('/api/livros', 'ApiAdminController@listarLivros');
$router->post('/api/livros', 'ApiAdminController@criarLivro');
$router->post('/api/livros/{livroId}/deletar', 'ApiAdminController@deletarLivro');

$router->get('/api/noticias', 'ApiAdminController@listarNoticias');
$router->post('/api/noticias', 'ApiAdminController@criarNoticia');
$router->post('/api/noticias/{noticiaId}/deletar', 'ApiAdminController@deletarNoticia');

$router->get('/api/auditoria', 'ApiAdminController@auditoria');

$router->get('/api/auth/check', 'ApiAuthController@check');

return $app;
