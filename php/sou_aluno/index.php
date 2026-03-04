<?php
require_once __DIR__ . '/../autenticacao.php';
requer_autenticacao();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sou Aluno — FETEL</title>
        <link rel="stylesheet" href="/TCC-etec/css/index.css">
        <link rel="stylesheet" href="/TCC-etec/css/sou_aluno.css">
    <meta name="robots" content="noindex">
    
</head>
<body>
        <header class="site-header">
        <div class="container header-inner">
                <a class="logo" href="/TCC-etec/index.html">
                    <img src="/TCC-etec/img/fetel_sem_fundo.png" alt="FETEL" class="logo-img" style="height:96px;width:auto;display:inline-block;vertical-align:middle;">
                </a>
            <nav class="nav" aria-label="Principal">
                <ul class="nav-list">
                    <li><a href="/TCC-etec/index.html">Início</a></li>
                    <li><a class="active-link" href="/TCC-etec/php/sou_aluno/index.php">Sou aluno</a></li>
                    <li><a href="/TCC-etec/php/secretaria/secretaria.php">Secretaria</a></li>
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
                <h1>Bem-vindo, aluno</h1>
                <p class="lead">Aqui você encontra seus cursos, materiais e solicitações rápidas. Tudo otimizado para velocidade e acessibilidade.</p>

                <div class="stats" role="region" aria-label="Resumo rápido">
                    
                    <div class="stat">
                        <div class="small-muted">Mensagens</div>
                        <div class="value" id="stat-messages">1</div>
                    </div>
                    <div class="stat">
                        <div class="small-muted">Emprestimos</div>
                        <div class="value" id="stat-loans">0</div>
                    </div>
                </div>

                                <div class="hero-cta">
                                    <div class="actions">
                                        <a class="btn primary" href="#portal">Portal Rápido</a>
                                        <a class="btn" href="/TCC-etec/php/login/esqueceuSenha.php">Redefinir senha</a>
                                    </div>
                                </div>
            </div>
        </section>

            
            <section class="section" id="portal">
                <div class="section-header">
                    <h2>Portal Rápido</h2>
                    <p class="section-sub">Acesse os serviços e notícias mais relevantes.</p>
                </div>

                <div class="cards small">
                    <div class="card">
                        <div class="icon" aria-hidden="true">📚</div>
                        <h3>Material Didático</h3>
                        <p>Baixe apostilas, slides e exercícios atualizados.</p>
                        <div class="flex"><a class="btn" href="#">Acessar</a></div>
                    </div>
                    <div class="card">
                        <div class="icon" aria-hidden="true">🧾</div>
                        <h3>Boletim</h3>
                        <p>Veja suas notas e frequência em um painel simples.</p>
                        <div class="flex"><a class="btn" href="#">Ver boletim</a></div>
                    </div>
                    <div class="card">
                        <div class="icon" aria-hidden="true">🏫</div>
                        <h3>Atendimento</h3>
                        <p>Abra solicitações para secretaria e biblioteca.</p>
                        <div class="flex"><a class="btn" href="#">Solicitar</a></div>
                    </div>
                </div>

                <div style="margin-top:18px">
                    <h3 style="margin-bottom:8px">Notícias</h3>
                    <ul class="news-list">
                        <li><a href="#">Abertura de matrículas para novo semestre</a> <span class="small-muted">— 05/01/2026</span></li>
                        <li><a href="#">Biblioteca: horário estendido nas segundas</a> <span class="small-muted">— 20/12/2025</span></li>
                        <li><a href="#">Plantão pedagógico online disponível</a> <span class="small-muted">— 15/12/2025</span></li>
                    </ul>
                </div>
            </section>

        <section class="section alt">
            <div class="section-header">
                <h2>Solicitações Rápidas</h2>
                <p class="section-sub">Acesse os serviços mais usados.</p>
            </div>
            <div class="cards small">
                <div class="card">
                    <div class="icon" aria-hidden="true">📄</div>
                    <h3>Histórico escolar</h3>
                    <p>Solicite seu histórico acadêmico de forma digital.</p>
                    <div class="flex"><a class="btn" href="#">Solicitar</a></div>
                </div>
                <div class="card">
                    <div class="icon" aria-hidden="true">🔔</div>
                    <h3>Atestado</h3>
                    <p>Emissão de atestado estudantil para transporte e convênios.</p>
                    <div class="flex"><a class="btn" href="#">Solicitar</a></div>
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

    <script src="/TCC-etec/js/painel_aluno.js" defer></script>
</body>
</html>
