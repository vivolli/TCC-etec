<?php require_once __DIR__ . '/../../header.php'; ?>

<main>

<section>

<h2>Catálogo</h2>
<p>Procure por título ou autor.</p>

<form method="get">

<input type="search" name="q"
placeholder="Pesquisar título ou autor..."
value="<?= htmlspecialchars($q ?? '', ENT_QUOTES); ?>">

<button type="submit">Buscar</button>

</form>

<hr>

<?php foreach ($books as $b): ?>

<article>

<h3><?= htmlspecialchars($b['titulo']); ?></h3>

<p>
<?= htmlspecialchars($b['autor']) ?> · <?= htmlspecialchars($b['ano']); ?>
</p>

<?php if (!empty($b['disponivel'])): ?>

<form method="post" action="/TCC-etec/php/biblioteca/emprestimo/emprestimo.php">

<input type="hidden" name="livro_id" value="<?= $b['id']; ?>">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()); ?>">

<button type="submit">
Solicitar
</button>

</form>

<?php else: ?>

<button disabled>
Reservar
</button>

<?php endif; ?>

<hr>

</article>

<?php endforeach; ?>

</section>

</main>