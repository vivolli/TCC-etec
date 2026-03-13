<?php
/**
 * Public/aluno.php
 * Página principal para alunos (dashboard)
 */

declare(strict_types=1);

// Carrega bootstrap com autoload
require_once __DIR__ . '/../Config/bootstrap.php';

use App\Model\Aluno as AlunoModel;
use App\Model\Usuario as UsuarioModel;

iniciar_sessao_segura();
requer_autenticacao();

// Verifica se é aluno
$info = getSessaoInfo();
$papel = strtolower((string)($info['papel'] ?? ''));

if ($papel !== 'aluno') {
    // Redireciona para a página apropriada se não for aluno
    if (in_array($papel, ['admin', 'adm', 'administrador', 'professor', 'prof', 'docente'], true)) {
        header('Location: /TCC-etec/Public/admin.php');
    } elseif (in_array($papel, ['secretaria', 'secretário', 'secretariao', 'secretaria_adj', 'funcionario'], true)) {
        header('Location: /TCC-etec/Public/secretaria.php');
    } else {
        header('Location: /TCC-etec/Public/login.php');
    }
    exit;
}

$usuario_id = $info['usuario_id'];
$usuario_email = $info['usuario_email'];
$usuario_nome = $info['usuario_nome'] ?? 'Aluno';

try {
    $db = Database::getInstance();
    $modeloAluno = new AlunoModel($db->getConnection());
    $modeloUsuario = new UsuarioModel($db->getConnection());

    // Busca dados completos do aluno
    $aluno = $modeloAluno->buscarCompleto($usuario_id);
    
    // Busca empréstimos ativos
    $emprestimos_ativos = $modeloAluno->buscarEmprestimosAtivos($usuario_id);
    
    // Busca noticias
    $noticias = $modeloUsuario->buscarNoticias(5);
    
    // Busca solicitações de secretaria
    $solicitacoes = $modeloAluno->buscarSolicitacoes($usuario_id);
    
} catch (\Exception $e) {
    error_log('Erro ao carregar dashboard do aluno: ' . $e->getMessage());
    $aluno = null;
    $emprestimos_ativos = [];
    $noticias = [];
    $solicitacoes = [];
}

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel do Aluno — FETEL</title>
    <link rel="stylesheet" href="/TCC-etec/Public/css/index.css">
    <link rel="stylesheet" href="/TCC-etec/Public/css/sou_aluno.css">
    <style>
        .dashboard {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .header-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .header-info h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        .header-info p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }
        .menu-aluno {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .menu-item {
            padding: 1.5rem;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            text-align: center;
            transition: transform 0.2s;
        }
        .menu-item:hover {
            transform: translateY(-5px);
        }
        .menu-item h3 {
            margin: 0 0 1rem 0;
            font-size: 1.2rem;
        }
        .menu-item p {
            margin: 0;
            font-size: 0.95rem;
        }
        .section {
            margin-bottom: 3rem;
        }
        .section h2 {
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .emprestimos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .emprestimo-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            transition: box-shadow 0.3s;
        }
        .emprestimo-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .emprestimo-card h4 {
            margin: 0 0 0.5rem 0;
            color: #333;
        }
        .emprestimo-card p {
            margin: 0.3rem 0;
            color: #666;
            font-size: 0.9rem;
        }
        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        .status-emprestado {
            background-color: #d4edda;
            color: #155724;
        }
        .status-atrasado {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-devolvido {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .noticias-grid {
            display: grid;
            gap: 1.5rem;
        }
        .noticia-card {
            background: white;
            border-left: 4px solid #667eea;
            border-radius: 4px;
            padding: 1.5rem;
            transition: box-shadow 0.3s;
        }
        .noticia-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .noticia-card h4 {
            margin: 0 0 0.5rem 0;
            color: #333;
        }
        .noticia-data {
            color: #999;
            font-size: 0.85rem;
        }
        .noticia-conteudo {
            margin-top: 0.8rem;
            color: #666;
            line-height: 1.5;
        }
        .vazio {
            text-align: center;
            padding: 2rem;
            color: #999;
            background: #f9f9f9;
            border-radius: 4px;
        }
        nav {
            display: flex;
            gap: 0;
        }
        nav a {
            flex: 1;
            padding: 1rem;
            text-align: center;
            border-bottom: 3px solid transparent;
            transition: border-color 0.3s;
        }
        nav a:hover {
            border-bottom-color: #667eea;
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="/TCC-etec/index.html">FETEL</a>
            <nav class="main-nav">
                <ul class="nav-list">
                    <li><a href="/TCC-etec/Public/aluno.php">Dashboard</a></li>
                    <li><a href="/TCC-etec/Public/biblioteca.php">Biblioteca</a></li>
                    <li><a href="/TCC-etec/Public/noticias.php">Notícias</a></li>
                    <li><a href="/TCC-etec/Public/login.php?logout=1">Sair</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="dashboard">
            <div class="header-info">
                <h1>Bem-vindo ao Painel do Aluno</h1>
                <p><?php echo htmlspecialchars($usuario_nome); ?> (<?php echo htmlspecialchars($usuario_email); ?>)</p>
                <?php if ($aluno): ?>
                    <p><strong>Matricula:</strong> <?php echo htmlspecialchars($aluno['matricula'] ?? 'N/A'); ?> | <strong>Curso:</strong> <?php echo htmlspecialchars($aluno['nome_curso'] ?? 'Não definido'); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="menu-aluno">
                <a href="/TCC-etec/Public/biblioteca.php" class="menu-item">
                    <h3>📚 Biblioteca</h3>
                    <p>Acesse nosso acervo de livros</p>
                </a>

                <a href="/TCC-etec/Public/catalogo.php" class="menu-item">
                    <h3>📖 Catálogo</h3>
                    <p>Consulte livros disponíveis</p>
                </a>

                <a href="/TCC-etec/Public/emprestimos.php" class="menu-item">
                    <h3>🔄 Meus Empréstimos</h3>
                    <p><?php echo count($emprestimos_ativos); ?> livro(s) emprestado(s)</p>
                </a>

                <a href="/TCC-etec/Public/noticias.php" class="menu-item">
                    <h3>📢 Notícias</h3>
                    <p>Fique atualizado com novidades</p>
                </a>
            </div>

            <?php if (!empty($emprestimos_ativos)): ?>
                <section class="section">
                    <h2>📚 Seus Empréstimos Ativos</h2>
                    <div class="emprestimos-grid">
                        <?php foreach ($emprestimos_ativos as $emp): ?>
                            <div class="emprestimo-card">
                                <h4><?php echo htmlspecialchars($emp['titulo']); ?></h4>
                                <p><strong>Autor:</strong> <?php echo htmlspecialchars($emp['autor'] ?? 'Desconhecido'); ?></p>
                                <p><strong>Emprestado em:</strong> <?php echo date('d/m/Y', strtotime($emp['emprestado_em'])); ?></p>
                                <p><strong>Vencimento:</strong> <?php echo date('d/m/Y', strtotime($emp['vencimento_em'])); ?></p>
                                <?php 
                                    $statusClass = $emp['status_real'] === 'atrasado' ? 'status-atrasado' : 'status-emprestado';
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php 
                                        if ($emp['status_real'] === 'atrasado') {
                                            echo 'Atrasado (' . $emp['dias_atraso'] . ' dias)';
                                        } else {
                                            echo 'Emprestado';
                                        }
                                    ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($noticias)): ?>
                <section class="section">
                    <h2>📢 Últimas Notícias</h2>
                    <div class="noticias-grid">
                        <?php foreach ($noticias as $noticia): ?>
                            <div class="noticia-card">
                                <h4><?php echo htmlspecialchars($noticia['titulo']); ?></h4>
                                <small class="noticia-data">
                                    Por <?php echo htmlspecialchars($noticia['autor_nome'] ?? 'Anônimo'); ?> 
                                    em <?php echo date('d/m/Y H:i', strtotime($noticia['publicado_em'])); ?>
                                </small>
                                <div class="noticia-conteudo">
                                    <?php echo htmlspecialchars(substr(strip_tags($noticia['conteudo']), 0, 200)) . '...'; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($solicitacoes)): ?>
                <section class="section">
                    <h2>📋 Suas Solicitações de Secretaria</h2>
                    <div style="background: white; border-radius: 8px; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9f9f9; border-bottom: 1px solid #ddd;">
                                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Tipo</th>
                                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Data</th>
                                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($solicitacoes as $sol): ?>
                                    <tr style="border-bottom: 1px solid #ddd;">
                                        <td style="padding: 1rem;"><?php echo htmlspecialchars($sol['tipo_solicitacao']); ?></td>
                                        <td style="padding: 1rem;"><?php echo date('d/m/Y H:i', strtotime($sol['criado_em'])); ?></td>
                                        <td style="padding: 1rem;">
                                            <span class="status-badge" style="background: <?php 
                                                echo match($sol['status']) {
                                                    'aberto' => '#e2e3e5',
                                                    'em_andamento' => '#fff3cd',
                                                    'encerrado' => '#d4edda',
                                                    'rejeitado' => '#f8d7da',
                                                    default => '#e2e3e5'
                                                };
                                            ?>; color: #333;">
                                                <?php echo ucfirst(str_replace('_', ' ', $sol['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 FETEL - Plataforma Escolar. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
