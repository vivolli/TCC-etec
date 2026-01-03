<?php
// página visual de recuperação de senha (somente front-end + resposta de confirmação)
// não envia e-mail — integre a lógica de envio onde indicado

$sent = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $error = 'Por favor informe um e-mail válido.';
    } else {
        // aqui você pode integrar a lógica de envio de e-mail (ex: enviar token/reset link)
        // exemplo: enviar_email_reset($email);
        $sent = true;
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperar senha — FETEL</title>
    <link rel="stylesheet" href="../../css/login.css">
    <meta name="robots" content="noindex">
</head>
<body>
    <main class="login-page">
        <a class="brand-link" href="../login/login.php" aria-label="Voltar ao login">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <rect x="2" y="2" width="20" height="20" rx="4" fill="#0056b3" />
                <path d="M6 16V8h3l3 6V8h3v8h-3l-3-6v6H6z" fill="#fff" />
            </svg>
            <span class="brand">FETEL</span>
        </a>

        <section class="login-card" aria-labelledby="forgotTitle">
            <h1 id="forgotTitle">Recuperar senha</h1>
            <p class="lead">Informe o e-mail associado à sua conta. Enviaremos instruções para redefinir a senha.</p>

            <?php if ($sent): ?>
                <div class="card" style="border-left:4px solid var(--blue); background:#fbfeff;">
                    <h3>Verifique seu e-mail</h3>
                    <p>Se existir uma conta com o e-mail informado, você receberá um link com instruções para redefinir sua senha.</p>
                    <a class="btn" href="../login/login.php">Voltar ao login</a>
                </div>
            <?php else: ?>

            <?php if ($error): ?>
                <div class="card" style="border-left:4px solid #f59e0b; background:#fff9ed;">
                    <p style="margin:0;color:#92400e"><?=htmlspecialchars($error)?></p>
                </div>
            <?php endif; ?>

            <form class="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required autocomplete="email" placeholder="seu@exemplo.com">

                <div style="margin-top:14px; display:flex; gap:12px; align-items:center;">
                    <button type="submit" class="btn primary">Enviar instruções</button>
                    <a class="btn" href="../login/login.php">Cancelar</a>
                </div>
            </form>

            <?php endif; ?>

            <p class="signup" style="margin-top:12px">Lembrou sua senha? <a href="../login/login.php">Entrar</a></p>
        </section>
    </main>

</body>
</html>
