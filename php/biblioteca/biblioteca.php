<?php
require_once __DIR__ . '/_acesso.php';
requer_aluno();
$info = get_aluno_info();
$nome = htmlspecialchars($info['nome'] ?? 'Aluno');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Biblioteca — FETEL</title>
    <link rel="stylesheet" href="/TCC-etec/css/index.css">
</head>
<body>
<?php require_once __DIR__ . '/../header.php'; ?>

<main class="container" style="padding:28px 0;">
    <section class="section">
        <div class="section-header">
            <h2>Bem-vindo à Biblioteca</h2>
            <p class="section-sub">Olá, <?php echo $nome; ?> — aqui estão seus recursos e empréstimos.</p>
        </div>

        <div class="grid">
            <div class="panel">
                <h3>Catálogo</h3>
                <p>Busque e solicite empréstimos do nosso acervo digital e físico.</p>
                <a class="btn primary" href="/TCC-etec/php/biblioteca/catalogo/index.php">Pesquisar catálogo</a>
            </div>

            <div class="panel">
                <h3>Meus Empréstimos</h3>
                <p>Ver tudo que você tem emprestado e solicitar renovação.</p>
                <a class="btn" href="/TCC-etec/php/biblioteca/emprestimo/emprestimo.php">Ver empréstimos</a>
            </div>

            <div class="panel">
                <h3>Regras</h3>
                <p>Prazo padrão: 14 dias. Renovação automática 1 vez se não houver reservas.</p>
                <a class="btn ghost" href="#">Política da biblioteca</a>
            </div>
        </div>
    </section>
</main>

<?php // minimal footer (no shared footer file available)
?>
</body>
</html>
</body>
</html>
