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
<img src="/TCC-etec/img/fetel_sem_fundo.png"
alt="FETEL"
class="logo-img"
style="height:72px;width:auto;display:inline-block;vertical-align:middle;">
</a>

<nav class="nav">
<ul class="nav-list">

<li><a href="/TCC-etec/index.html">Início</a></li>

<li>
<a class="active-link"
href="/TCC-etec/php/login/adms/logado/index.php">
Painel
</a>
</li>

<li>
<a href="/TCC-etec/php/secretaria/secretaria.php">
Secretaria
</a>
</li>

<li>
<a href="/TCC-etec/php/login/adms/usuarios.php">
Usuários
</a>
</li>

<li class="nav-logout">
<a href="/TCC-etec/php/sair.php">Sair</a>
</li>

</ul>
</nav>

</div>
</header>


<main class="container">

<section class="hero dashboard-hero">

<div class="hero-inner">

<h1>Bem-vindo, <?= $nome ?></h1>

<p class="lead">
Área administrativa — aqui você gerencia usuários,
empréstimos e relatórios.
</p>


<div class="stats">

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

<a class="btn primary" href="#portal">
Portal Administrativo
</a>

<a class="btn"
href="/TCC-etec/php/login/esqueceuSenha.php">
Redefinir senha
</a>

</div>

</div>

</div>

</section>


<section class="section" id="portal">

<div class="section-header">
<h2>Portal Administrativo</h2>
<p class="section-sub">
Acesse as ferramentas de gestão rápido.
</p>
</div>


<div class="cards small">

<div class="card">

<div class="icon">👥</div>

<h3>Gerenciar Usuários</h3>

<p>
Crie, edite e desative contas de alunos,
professores e admins.
</p>

<a class="btn"
href="/TCC-etec/php/login/adms/usuarios.php">
Abrir
</a>

</div>


<div class="card">

<div class="icon">📚</div>

<h3>Empréstimos</h3>

<p>
Veja e gerencie livros e materiais emprestados.
</p>

<a class="btn"
href="/TCC-etec/php/emprestimos/emprestimo.php">
Ver
</a>

</div>


<div class="card">

<div class="icon">📊</div>

<h3>Relatórios</h3>

<p>
Gere relatórios de alunos, frequência
e uso de recursos.
</p>

<a class="btn"
href="/TCC-etec/php/secretaria/secretaria.php">
Gerar
</a>

</div>

</div>

</section>

</main>


<footer class="site-footer">

<div class="container footer-inner">

<div>
<p><strong>FETEL</strong></p>
<p class="small-muted">
Rua Exemplo, 123 — Cidade
</p>
</div>

<div>
<p class="small-muted">
© <span id="year"></span> FETEL
</p>
</div>

</div>

</footer>


<script src="/TCC-etec/js/painel_adm.js" defer></script>

</body>
</html>