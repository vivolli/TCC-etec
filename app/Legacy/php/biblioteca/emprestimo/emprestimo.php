<?php
// canonical copy of php/biblioteca/emprestimo/emprestimo.php
require_once __DIR__ . '/../../_acesso.php';
requer_aluno();
$info = get_aluno_info();
if (function_exists('iniciar_sessao_segura')) {
    iniciar_sessao_segura();
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$usuario_id = $_SESSION['usuario_id'] ?? null;

$message = null;
try {
    require_once __DIR__ . '/../../../../db/conexao.php';
    $pdo = null;
    if (function_exists('getPDO')) $pdo = getPDO();
} catch (Throwable $e) {
    $pdo = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livro_id'])) {
    $livroId = intval($_POST['livro_id']);
    $csrf = $_POST['csrf'] ?? '';
    if (!validate_csrf($csrf)) {
        $message = 'Token de segurança inválido. Tente novamente.';
    } else {
        if ($pdo instanceof PDO && $usuario_id) {
            $stmt = $pdo->prepare('INSERT INTO emprestimos (usuario_id, livro_id, data_solicitacao, status) VALUES (:uid, :lid, NOW(), :st)');
            try {
                $stmt->execute([':uid' => $usuario_id, ':lid' => $livroId, ':st' => 'solicitado']);
                $message = 'Empréstimo solicitado com sucesso.';
            } catch (Throwable $e) {
                error_log('Erro DB emprestimo: ' . $e->getMessage());
                $message = 'Erro ao solicitar empréstimo (DB).';
            }
        } else {
            $dir = __DIR__ . '/data';
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
            $file = $dir . '/loans_' . ($usuario_id ?? 'guest') . '.json';
            $loans = [];
            if (file_exists($file)) {
                $loans = json_decode(file_get_contents($file), true) ?: [];
            }
            $loans[] = ['livro_id' => $livroId, 'data' => date('c'), 'status' => 'solicitado'];
            file_put_contents($file, json_encode($loans, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
            $message = 'Empréstimo solicitado (modo offline).';
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['acao']) || isset($_GET['id']))) {
    // Deprecated: solicitations must be POST. Reject or instruct the client.
    $message = 'Solicitações devem ser feitas via formulário (POST) por segurança.';
}

// read current loans
$loans = [];
if ($pdo instanceof PDO && $usuario_id) {
    try {
        $q = $pdo->prepare('SELECT e.id, e.livro_id, l.titulo, e.data_solicitacao, e.status FROM emprestimos e LEFT JOIN livros l ON l.id = e.livro_id WHERE e.usuario_id = :uid ORDER BY e.data_solicitacao DESC');
        $q->execute([':uid' => $usuario_id]);
        $loans = $q->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $loans = [];
    }
} else {
    $file = __DIR__ . '/data/loans_' . ($usuario_id ?? 'guest') . '.json';
    if (file_exists($file)) {
        $loans = json_decode(file_get_contents($file), true) ?: [];
    }
}

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Meus Empréstimos — Biblioteca</title>
    <link rel="stylesheet" href="/TCC-etec/app/public/css/index.css">
</head>
<body>
<?php require_once __DIR__ . '/../../header.php'; ?>

<main class="container" style="padding:28px 0;">
    <section class="section">
        <div class="section-header">
            <h2>Meus Empréstimos</h2>
            <p class="section-sub">Acompanhe o status das suas solicitações.</p>
        </div>

        <?php if ($message): ?>
            <div class="panel">
                <strong><?php echo htmlspecialchars($message); ?></strong>
            </div>
        <?php endif; ?>

        <div class="cards small">
            <?php if (empty($loans)): ?>
                <div class="panel">Você não tem empréstimos no momento.</div>
            <?php else: ?>
                <?php foreach ($loans as $l): ?>
                    <article class="card">
                        <h3><?php echo htmlspecialchars($l['titulo'] ?? ('#' . ($l['livro_id'] ?? ''))); ?></h3>
                        <p>Status: <?php echo htmlspecialchars($l['status'] ?? '—'); ?></p>
                        <p>Solicitado em: <?php echo htmlspecialchars($l['data_solicitacao'] ?? ($l['data'] ?? '—')); ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

</body>
</html>


