<?php require_once __DIR__ . '/../../header.php'; ?>

<h2>Meus Empréstimos</h2>

<?php if ($message): ?>

<p><strong><?= htmlspecialchars($message) ?></strong></p>

<?php endif; ?>

<?php if (empty($loans)): ?>

<p>Você não tem empréstimos no momento.</p>

<?php else: ?>

<?php foreach ($loans as $l): ?>

<article>

<h3><?= htmlspecialchars($l['titulo'] ?? ('#'.$l['livro_id'])) ?></h3>

<p>Status: <?= htmlspecialchars($l['status']) ?></p>

<p>Solicitado em: <?= htmlspecialchars($l['data_solicitacao']) ?></p>

</article>

<hr>

<?php endforeach; ?>

<?php endif; ?>