<?php
// header.php - cabeçalho reutilizavel
?>

<link rel="stylesheet" href="/TCC/css/index.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<header class="site-header">
  <div class="container header-inner">
    <a class="logo" href="/TCC/index.html" aria-label="FETEL - Início">
      <svg width="42" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <rect x="2" y="2" width="20" height="20" rx="4" fill="#0056b3" />
        <path d="M6 16V8h3l3 6V8h3v8h-3l-3-6v6H6z" fill="#fff" />
      </svg>
      <span class="brand">FETEL</span>
    </a>

    <button class="nav-toggle" aria-label="Abrir menu" aria-expanded="false">
      <span class="hamburger"></span>
    </button>

    <nav class="nav" id="main-nav">
      <ul class="nav-list">
        <li><a href="/TCC/index.html#destaques">Notícias</a></li>
        <li><a href="/TCC/php/secretaria.php">Secretaria</a></li>
        <li><a href="/TCC/index.html#biblioteca">Biblioteca</a></li>
        <li><a href="/TCC/index.html#sobre">Sobre a Escola</a></li>
      </ul>
    </nav>
  </div>
</header>
