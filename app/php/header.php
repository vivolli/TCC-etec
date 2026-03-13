<?php
// header.php - cabeçalho reutilizavel
// Try to initialize app bootstrap (non-fatal)
$bootstrap = __DIR__ . '/../Core/Bootstrap.php';
if (file_exists($bootstrap)) {
    @include_once $bootstrap;
}
?>

<link rel="stylesheet" href="/TCC-etec/app/public/css/index.css">
<link rel="stylesheet" href="/TCC-etec/app/public/css/noticias.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<header class="site-header">
  <div class="container header-inner">
    <a class="logo" href="/TCC-etec/" aria-label="FETEL - Início">
      <img src="/TCC-etec/app/public/img/fetel_sem_fundo.png" alt="FETEL" style="height:96px; width:auto; display:inline-block; vertical-align:middle;">
    </a>

    <button class="nav-toggle" aria-label="Abrir menu" aria-expanded="false">
      <span class="hamburger"></span>
    </button>

    <nav class="nav" id="main-nav">
      <ul class="nav-list">
        <li><a href="/TCC-etec/#destaques">Notícias</a></li>
        <li><a href="/TCC-etec/app/php/secretaria/secretaria.php">Secretaria</a></li>
        <li><a href="/TCC-etec/#biblioteca">Biblioteca</a></li>
        <li><a href="/TCC-etec/#sobre">Sobre a Escola</a></li>
      </ul>
    </nav>

    <?php
    // display simple user greeting when available (professional, no emoji)
    if (function_exists('getSessaoInfo')) {
        $s = getSessaoInfo();
        if (!empty($s['logado'])) {
            $name = htmlspecialchars((string)($s['nome'] ?? ''), ENT_QUOTES);
            echo '<div class="header-user" style="margin-left:18px">';
            echo '<a class="btn" href="/TCC-etec/">Bem-vindo, ' . ($name ?: 'Usuário') . '</a>';
            echo '</div>';
        }
    }
    ?>
  </div>
</header>


