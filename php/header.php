<?php
// header.php - cabeçalho reutilizavel
?>

<link rel="stylesheet" href="/TCC-etec/css/index.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<header class="site-header">
  <div class="container header-inner">
      <a class="logo" href="/TCC-etec/index.html" aria-label="FETEL - Início">
  <img src="/TCC-etec/img/fetel_sem_fundo.png" alt="FETEL" style="height:96px; width:auto; display:inline-block; vertical-align:middle;">
    </a>

    <button class="nav-toggle" aria-label="Abrir menu" aria-expanded="false">
      <span class="hamburger"></span>
    </button>

    <nav class="nav" id="main-nav">
      <ul class="nav-list">
        <li><a href="/TCC-etec/index.html#destaques">Notícias</a></li>
        <li><a href="/TCC-etec/php/secretaria.php">Secretaria</a></li>
        <li><a href="/TCC-etec/index.html#biblioteca">Biblioteca</a></li>
        <li><a href="/TCC-etec/index.html#sobre">Sobre a Escola</a></li>
      </ul>
    </nav>
  </div>
</header>
