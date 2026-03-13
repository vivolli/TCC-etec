<?php require_once __DIR__ . '/../../header.php'; ?>

<h2>Bem-vindo à Biblioteca</h2>

<p>Olá, <?= $nome ?> — aqui estão seus recursos e empréstimos.</p>

<div>

<h3>Catálogo</h3>
<p>Busque e solicite empréstimos do nosso acervo.</p>
<a href="/TCC-etec/public/catalogo.php">Pesquisar catálogo</a>

</div>

<div>

<h3>Meus Empréstimos</h3>
<p>Ver tudo que você tem emprestado.</p>
<a href="/TCC-etec/public/emprestimos.php">Ver empréstimos</a>

</div>

<div>

<h3>Regras</h3>
<p>Prazo padrão: 14 dias. Renovação automática se não houver reservas.</p>

</div>