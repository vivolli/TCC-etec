<?php
require_once __DIR__ . '/../../biblioteca/_acesso.php';
requer_aluno();
$info = get_aluno_info();

$books = [];
try {
    require_once __DIR__ . '/../../../db/conexao.php';
    $pdo = null;
    if (function_exists('getPDO')) {
        $pdo = getPDO();
    }
    if ($pdo instanceof PDO) {
        $q = $pdo->prepare('SELECT id, titulo, autor, ano, disponivel FROM livros ORDER BY titulo LIMIT 200');
        $ok = $q->execute();
        if ($ok) $books = $q->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
}

if (empty($books)) {
    $books = [
        ['id' => 1, 'titulo' => 'Algoritmos e Lógica', 'autor' => 'Maria Silva', 'ano' => '2019', 'disponivel' => 1],
        ['id' => 2, 'titulo' => 'Introdução ao PHP', 'autor' => 'João Souza', 'ano' => '2020', 'disponivel' => 1],
        ['id' => 3, 'titulo' => 'Redes e Infraestrutura', 'autor' => 'Carlos Pereira', 'ano' => '2018', 'disponivel' => 0],
    ];
}

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Catálogo — Biblioteca FETEL</title>
    <link rel="stylesheet" href="/TCC-etec/css/index.css">
</head>
<body>
<?php require_once __DIR__ . '/../../header.php'; ?>

<main class="container" style="padding:28px 0;">
    <section class="section">
        <div class="section-header">
            <h2>Catálogo</h2>
            <p class="section-sub">Procure por título, autor ou ano.</p>
        </div>

        <div class="contact-form">
            <form method="get">
                <div class="row">
                    <input type="search" name="q" placeholder="Pesquisar título ou autor..." value="<?php echo htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES); ?>">
                    <button class="btn" type="submit">Buscar</button>
                </div>
            </form>
        </div>

        <div class="cards small" style="margin-top:18px;">
            <?php
            $q = trim((string)($_GET['q'] ?? ''));
            foreach ($books as $b):
                if ($q !== '') {
                    $found = stripos($b['titulo'] . ' ' . $b['autor'] . ' ' . ($b['ano'] ?? ''), $q) !== false;
                    if (!$found) continue;
                }
            ?>
            <article class="card">
                <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path fill="#0056b3" d="M6 2h9a3 3 0 013 3v13a1 1 0 01-1 1H6a1 1 0 01-1-1V3a1 1 0 011-1z"></path></svg></div>
                <h3><?php echo htmlspecialchars($b['titulo']); ?></h3>
                <p><?php echo htmlspecialchars($b['autor']) . ' · ' . htmlspecialchars($b['ano']); ?></p>
                <div class="flex">
                        <?php if (!empty($b['disponivel'])): ?>
                            <form method="post" action="/TCC-etec/php/biblioteca/emprestimo/emprestimo.php" style="margin:0">
                                <input type="hidden" name="livro_id" value="<?php echo htmlspecialchars($b['id']); ?>">
                                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <button type="submit" class="btn primary">Solicitar</button>
                            </form>
                        <?php else: ?>
                            <button class="btn ghost" disabled aria-disabled="true">Reservar</button>
                        <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

</body>
</html>
