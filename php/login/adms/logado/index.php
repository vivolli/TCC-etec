<?php
require_once __DIR__ . '/../../../autenticacao.php';

iniciar_sessao_segura();
if (!esta_logado()) {
    header('Location: /TCC-etec/php/login/entrar.php');
    exit;
}

requer_autenticacao();

$nome = htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8');
$papel = htmlspecialchars($_SESSION['usuario_papel'] ?? 'adm', ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
    <html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Painel Administrativo — FETEL</title>
        <link rel="stylesheet" href="/TCC-etec/css/index.css">
        <link rel="stylesheet" href="/TCC-etec/css/sou_aluno.css">
        <meta name="robots" content="noindex">
    </head>
    <body>
        <header class="site-header">
            <div class="container header-inner">
                <a class="logo" href="/TCC-etec/index.html">
                    <img src="/TCC-etec/img/fetel_sem_fundo.png" alt="FETEL" class="logo-img" style="height:72px;width:auto;display:inline-block;vertical-align:middle;">
                </a>
                <nav class="nav" aria-label="Principal">
                    <ul class="nav-list">
                        <li><a href="/TCC-etec/index.html">Início</a></li>
                        <li><a class="active-link" href="/TCC-etec/php/login/adms/logado/index.php">Painel</a></li>
                        <li><a href="/TCC-etec/php/secretaria/secretaria.php">Secretaria</a></li>
                        <li><a href="/TCC-etec/php/login/adms/usuarios.php">Usuários</a></li>
                        <li class="nav-logout"><a href="/TCC-etec/php/sair.php">Sair</a></li>
                        <li class="nav-divider" aria-hidden="true"></li>
                        <li class="nav-user"><a class="user-link" href="#" aria-label="Perfil do usuário"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3" stroke="var(--blue)" stroke-width="1.5"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6" stroke="var(--blue)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main class="container">
            <section class="hero dashboard-hero">
                <div class="hero-inner">
                    <h1>Bem-vindo, <?php echo $nome; ?></h1>
                    <p class="lead">Área administrativa — aqui você gerencia usuários, empréstimos e relatórios.</p>

                    <div class="stats" role="region" aria-label="Resumo rápido">
                        <div class="stat">
                            <div class="small-muted">Usuários ativos</div>
                            <div class="value" id="stat-users">—</div>
                        </div>
                        <div class="stat">
                            <div class="small-muted">Empréstimos pendentes</div>
                            <div class="value" id="stat-loans">—</div>
                        </div>
                        <div class="stat">
                            <div class="small-muted">Mensagens</div>
                            <div class="value" id="stat-messages">—</div>
                        </div>
                    </div>

                    <div class="hero-cta">
                        <div class="actions">
                            <a class="btn primary" href="#portal">Portal Administrativo</a>
                            <a class="btn" href="/TCC-etec/php/login/esqueceuSenha.php">Redefinir senha</a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section" id="portal">
                <div class="section-header">
                    <h2>Portal Administrativo</h2>
                    <p class="section-sub">Acesse as ferramentas de gestão rápido.</p>
                </div>

                <div class="cards small">
                    <div class="card action-card" data-action="usuarios">
                        <div class="icon" aria-hidden="true">👥</div>
                        <h3>Gerenciar Usuários</h3>
                        <p>Crie, edite e desative contas de alunos, professores e admins.</p>
                        <div class="flex"><a class="btn" href="/TCC-etec/php/login/adms/usuarios.php">Abrir</a></div>
                    </div>
                    <div class="card action-card" data-action="emprestimos">
                        <div class="icon" aria-hidden="true">📚</div>
                        <h3>Empréstimos</h3>
                        <p>Veja e gerencie livros e materiais emprestados.</p>
                        <div class="flex"><a class="btn" href="/TCC-etec/php/emprestimos/emprestimo.php">Ver</a></div>
                    </div>
                    <div class="card action-card" data-action="relatorios">
                        <div class="icon" aria-hidden="true">📊</div>
                        <h3>Relatórios</h3>
                        <p>Gere relatórios de alunos, frequência e uso de recursos.</p>
                        <div class="flex"><a class="btn" href="/TCC-etec/php/secretaria/secretaria.php">Gerar</a></div>
                    </div>
                </div>

                <div style="margin-top:18px">
                    <h3 style="margin-bottom:8px">Notícias e Avisos</h3>
                    <ul class="news-list" id="admin-news">
                        <li><a href="#">Painel inicializando... atualize para ver notícias reais</a> <span class="small-muted">— agora</span></li>
                    </ul>
                </div>
            </section>

            <section class="section alt">
                <div class="section-header">
                    <h2>Ações Rápidas</h2>
                    <p class="section-sub">Acesse tarefas administrativas comuns.</p>
                </div>
                <div class="cards small">
                    <div class="card">
                        <div class="icon" aria-hidden="true">🔔</div>
                        <h3>Notificações</h3>
                        <p>Enviar comunicados para todos os alunos.</p>
                        <div class="flex"><button class="btn" id="btn-notify">Enviar</button></div>
                    </div>
                    <div class="card">
                        <div class="icon" aria-hidden="true">⚙️</div>
                        <h3>Configurações</h3>
                        <p>Ajuste parâmetros do sistema e integrações.</p>
                        <div class="flex"><a class="btn" href="#">Abrir</a></div>
                    </div>
                </div>
            </section>

        </main>

        <footer class="site-footer">
            <div class="container footer-inner">
                <div class="contacts">
                    <p><strong>FETEL</strong></p>
                    <p class="small-muted">Rua Exemplo, 123 — Cidade</p>
                </div>
                <div>
                    <p class="small-muted">&copy; <span id="year"></span> FETEL. Todos os direitos reservados.</p>
                </div>
            </div>
        </footer>

        <script src="/TCC-etec/js/painel_adm.js" defer></script>
    </body>
    </html>
