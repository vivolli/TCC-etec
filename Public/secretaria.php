<?php
/**
 * Public/secretaria.php
 * Página principal para secretárias/secretários
 */

declare(strict_types=1);

// Carrega bootstrap com autoload
require_once __DIR__ . '/../Config/bootstrap.php';

use App\Model\Funcionario as FuncionarioModel;

iniciar_sessao_segura();
requer_autenticacao();

// Verifica se é secretária/funcionário
$info = getSessaoInfo();
$papel = strtolower((string)($info['papel'] ?? ''));

if (!in_array($papel, ['secretaria', 'secretário', 'secretariao', 'secretaria_adj', 'funcionario'], true)) {
    // Redireciona para a página apropriada se não for secretária
    if (in_array($papel, ['admin', 'adm', 'administrador', 'professor', 'prof', 'docente'], true)) {
        header('Location: /TCC-etec/Public/admin.php');
    } elseif ($papel === 'aluno') {
        header('Location: /TCC-etec/Public/aluno.php');
    } else {
        header('Location: /TCC-etec/Public/login.php');
    }
    exit;
}

$usuario_id = $info['usuario_id'];
$usuario_email = $info['usuario_email'];
$usuario_nome = $info['usuario_nome'] ?? 'Funcionário';

try {
    $db = Database::getInstance();
    $modeloFuncionario = new FuncionarioModel($db->getConnection());

    // Busca informações do funcionário
    $funcionario = $modeloFuncionario->buscarCompleto($usuario_id);
    
    // Busca solicitações abertas
    $solicitacoes_abertas = $modeloFuncionario->buscarSolicitacoesPorStatus('aberto', 20);
    
    // Busca empréstimos atrasados
    $emprestimos_atrasados = $modeloFuncionario->buscarEmprestimosAtrasados(10);
    
    // Busca estatísticas
    $stats = $modeloFuncionario->obterEstatisticasBiblioteca();
    
} catch (\Exception $e) {
    error_log('Erro ao carregar dashboard da secretaria: ' . $e->getMessage());
    $funcionario = null;
    $solicitacoes_abertas = [];
    $emprestimos_atrasados = [];
    $stats = [];
}

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel da Secretaria — FETEL</title>
    <link rel="stylesheet" href="/TCC-etec/Public/css/index.css">
    <style>
        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .header-info {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #f5576c;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .stat-card h3 {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 600;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #f5576c;
            margin: 1rem 0 0 0;
        }
        .section {
            margin-bottom: 3rem;
        }
        .section h2 {
            color: #333;
            border-bottom: 2px solid #f5576c;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .requests-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .requests-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .requests-table thead {
            background: #f9f9f9;
            border-bottom: 2px solid #ddd;
        }
        .requests-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        .requests-table td {
            padding: 1rem;
            border-bottom: 1px solid #ddd;
        }
        .requests-table tbody tr:hover {
            background: #f9f9f9;
        }
        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-aberto {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .status-em_andamento {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-encerrado {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejeitado {
            background-color: #f8d7da;
            color: #721c24;
        }
        .priority-alerta {
            color: #f5576c;
            font-weight: 600;
        }
        .priority-aviso {
            color: #ff9800;
            font-weight: 600;
        }
        .priority-critico {
            color: #d32f2f;
            font-weight: 600;
        }
        .vazio {
            text-align: center;
            padding: 2rem;
            color: #999;
            background: #f9f9f9;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="/TCC-etec/index.html">FETEL</a>
            <nav class="main-nav">
                <ul class="nav-list">
                    <li><a href="/TCC-etec/Public/secretaria.php">Dashboard</a></li>
                    <li><a href="#">Alunos</a></li>
                    <li><a href="#">Documentos</a></li>
                    <li><a href="/TCC-etec/Public/login.php?logout=1">Sair</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="dashboard">
            <div class="header-info">
                <h1>Bem-vindo ao Painel da Secretaria</h1>
                <p><?php echo htmlspecialchars($usuario_nome); ?> (<?php echo htmlspecialchars($usuario_email); ?>)</p>
                <?php if ($funcionario): ?>
                    <p><strong>Cargo:</strong> <?php echo htmlspecialchars($funcionario['cargo'] ?? 'Não definido'); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($stats)): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>📚 Total de Livros</h3>
                        <div class="stat-number"><?php echo $stats['livros']['total'] ?? 0; ?></div>
                        <small style="color: #999;"><?php echo $stats['livros']['disponiveis'] ?? 0; ?> disponíveis</small>
                    </div>

                    <div class="stat-card" style="border-left-color: #4caf50;">
                        <h3>🔄 Empréstimos Ativos</h3>
                        <div class="stat-number" style="color: #4caf50;"><?php echo $stats['emprestimos_ativos'] ?? 0; ?></div>
                    </div>

                    <div class="stat-card" style="border-left-color: #ff9800;">
                        <h3>⚠️ Atrasados</h3>
                        <div class="stat-number" style="color: #ff9800;"><?php echo $stats['emprestimos_atrasados'] ?? 0; ?></div>
                    </div>

                    <div class="stat-card" style="border-left-color: #2196f3;">
                        <h3>👥 Usuários Ativos</h3>
                        <div class="stat-number" style="color: #2196f3;"><?php echo $stats['usuarios_ativos'] ?? 0; ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($solicitacoes_abertas)): ?>
                <section class="section">
                    <h2>📋 Solicitações de Secretaria Abertas</h2>
                    <div class="requests-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Tipo de Solicitação</th>
                                    <th>Data</th>
                                    <th>Detalhes</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($solicitacoes_abertas as $sol): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($sol['nome_completo']); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $sol['tipo_solicitacao']))); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($sol['criado_em'])); ?></td>
                                        <td><?php echo htmlspecialchars(substr($sol['detalhes'] ?? '', 0, 50)); ?><?php echo strlen($sol['detalhes'] ?? '') > 50 ? '...' : ''; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo str_replace(' ', '_', $sol['status']); ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $sol['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" style="color: #f5576c; text-decoration: none; font-weight: 600;">Ver mais</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php else: ?>
                <section class="section">
                    <h2>📋 Solicitações de Secretaria Abertas</h2>
                    <div class="vazio">
                        <p>Nenhuma solicitação aberta no momento.</p>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($emprestimos_atrasados)): ?>
                <section class="section">
                    <h2>⚠️ Empréstimos Atrasados</h2>
                    <div class="requests-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Livro</th>
                                    <th>Autor</th>
                                    <th>Data de Vencimento</th>
                                    <th>Dias de Atraso</th>
                                    <th>Prioridade</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($emprestimos_atrasados as $emp): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($emp['nome_completo']); ?></td>
                                        <td><?php echo htmlspecialchars($emp['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($emp['autor'] ?? 'Desconhecido'); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($emp['vencimento_em'])); ?></td>
                                        <td><?php echo $emp['dias_atraso']; ?> dias</td>
                                        <td>
                                            <span class="priority-<?php echo $emp['prioridade']; ?>">
                                                <?php echo ucfirst($emp['prioridade']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" style="color: #f5576c; text-decoration: none; font-weight: 600;">Contatar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php else: ?>
                <section class="section">
                    <h2>⚠️ Empréstimos Atrasados</h2>
                    <div class="vazio">
                        <p>Nenhum empréstimo atrasado.</p>
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
