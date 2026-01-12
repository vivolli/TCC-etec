<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
if (file_exists(__DIR__ . '/phpmailer/vendor/autoload.php')) {
    require_once __DIR__ . '/phpmailer/vendor/autoload.php';
}

require_once __DIR__ . '/../../db/conexao.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

$sent = false;
$error = '';
if (isset($_GET['sent']) && $_GET['sent'] == '1') {
    $sent = true;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $error = 'Por favor informe um e-mail válido.';
    } else {
        try {
            $pdo = null;
            try {
                $pdo = getPDO();
            } catch (Throwable $ex) {
                $pdo = null;
            }
            $mail = new PHPMailer(true);
            $mail->CharSet    = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = $_ENV['EMAIL_HOST'] ?? '';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['EMAIL_USER'] ?? '';
            $mail->Password   = $_ENV['EMAIL_PASSWORD'] ?? '';
            $port = isset($_ENV['EMAIL_PORT']) ? (int)$_ENV['EMAIL_PORT'] : 465;
            $mail->Port = $port;
            if ($port === 587) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }

            $fromEmail = $_ENV['EMAIL_USER'] ?? $mail->Username;
            $fromName = $_ENV['EMAIL_FROM_NAME'] ?? 'FETEL';
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($email, 'Usuário(a)');
            $mail->addReplyTo($fromEmail, $fromName);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de senha — FETEL';

            $imageAttachment = __DIR__ . '/../../img/fetel.png';
            $embeddedHtml = '';
            if (file_exists($imageAttachment)) {
                $mail->addEmbeddedImage($imageAttachment, 'banner1', 'fetel.png');
                $embeddedHtml = "<div style='text-align:center;margin-top:18px'><img src='cid:banner1' alt='FETEL' style='width:100%;max-width:520px;height:auto;border-radius:8px;display:block;margin:0 auto'></div>";
            }

            $resetLinkHtml = '';
            if ($pdo) {
                $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                $u = $stmt->fetch();
                $usuarioId = $u ? (int)$u['id'] : null;

                $token = bin2hex(random_bytes(16));
                $tokenHash = hash('sha256', $token);
                $expires = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

                try {
                    $colStmt = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'redefinicoes_senha'");
                    $cols = $colStmt->fetchAll(PDO::FETCH_COLUMN);
                } catch (Throwable $colEx) {
                    $cols = [];
                }

                $insertCols = [];
                $insertValues = [];
                if (in_array('usuario_id', $cols)) {
                    $insertCols[] = 'usuario_id';
                    $insertValues[] = $usuarioId;
                }
                if (in_array('email', $cols)) {
                    $insertCols[] = 'email';
                    $insertValues[] = $email;
                }
                if (in_array('token_hash', $cols)) {
                    $insertCols[] = 'token_hash';
                    $insertValues[] = $tokenHash;
                }
                if (in_array('expires_at', $cols)) {
                    $insertCols[] = 'expires_at';
                    $insertValues[] = $expires;
                }

                if (!empty($insertCols)) {
                    $placeholders = implode(',', array_fill(0, count($insertCols), '?'));
                    $sql = 'INSERT INTO redefinicoes_senha (' . implode(',', $insertCols) . ') VALUES (' . $placeholders . ')';
                    $ins = $pdo->prepare($sql);
                    $ins->execute($insertValues);
                } else {
                }

                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
                $resetUrl = $scheme . '://' . $host . '/TCC-etec/php/login/reset.php?token=' . urlencode($token);

                $resetLinkHtml = "<p style='margin-top:18px'>Clique no link abaixo para redefinir sua senha. O link é válido por 1 hora:</p>";
                $resetLinkHtml .= "<p style='margin-top:8px'><a href='" . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . "' target='_blank'>Redefinir minha senha</a></p>";
            }

            $mailBodyHtml = <<<HTML
                <div style="font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Arial; color:#0f172a">
                    <h2>Olá,</h2>
                    <h3>Recebemos uma solicitação para redefinir sua senha. Se você solicitou a redefinição, siga as instruções enviadas neste e-mail.</h3>
                    {$resetLinkHtml}
                    <p style="margin-top:18px">Atenciosamente,<br><strong>FETEL</strong></p>
                </div>
                HTML;

                $mail->Body = $mailBodyHtml . $embeddedHtml;
                $mail->AltBody = "Recebemos uma solicitação para redefinir sua senha. Se você solicitou a redefinição, siga as instruções enviadas neste e-mail.\n\nSe você não solicitou, ignore esta mensagem.\n\nAtenciosamente, FETEL";

            $mail->send();
            $location = basename($_SERVER['PHP_SELF']);
            header('Location: ' . $location . '?sent=1');
            exit;
        } catch (Exception $e) {
            $error = 'Não foi possível enviar o e-mail. Erro: ' . $mail->ErrorInfo;
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperar senha — FETEL</title>
    <link rel="stylesheet" href="/TCC-etec/css/login.css">
    <link rel="stylesheet" href="/TCC-etec/css/esqueceu_senha.css">
    <meta name="robots" content="noindex">
</head>
<body>
    <script src="/TCC-etec/js/esqueceu_senha.js" defer></script>
    <?php if ($sent): ?>
        <div class="toast success" role="status" aria-live="polite">
            <div>
                <strong>Email de recuperacao enviado com sucesso</strong>
                <div style="font-size:.95rem;margin-top:6px;color:rgba(0,0,0,0.7)">Verifique o e-mail informado para instruções.</div>
            </div>
            <button class="close" aria-label="Fechar">✕</button>
        </div>
    <?php 
    elseif ($error): ?>
        <div class="toast error" role="alert">
            <div>
                <strong>Erro ao enviar e-mail</strong>
                <div style="font-size:.95rem;margin-top:6px;color:rgba(0,0,0,0.7)"><?=htmlspecialchars($error)?></div>
            </div>
            <button class="close" aria-label="Fechar">✕</button>
        </div>
    <?php endif; ?>
    <main class="login-page">
        <a class="brand-link" href="/TCC-etec/index.html" aria-label="Voltar ao inicio">
            <img src="/TCC-etec/img/fetel_sem_fundo.png" alt="FETEL" style="height:96px; width:auto; display:inline-block; vertical-align:middle;">
        </a>

        <section class="login-card" aria-labelledby="forgotTitle">
            <h1 id="forgotTitle">Recuperar senha</h1>
            <p class="lead">Informe o e-mail associado à sua conta. Enviaremos instruções para redefinir a senha.</p>

            <?php if ($sent): ?>
                <div class="card" style="border-left:4px solid var(--blue); background:#fbfeff;">
                    <h3>Verifique seu e-mail</h3>
                    <p>Se existir uma conta com o e-mail informado, você receberá um link com instruções para redefinir sua senha.</p>
                    <a class="btn" href="login.php">Voltar ao login</a>
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
                    <a class="btn" href="login.html">Cancelar</a>
                </div>
            </form>

            <?php endif; ?>

            <p class="signup" style="margin-top:12px">Lembrou sua senha? <a href="login.html">Entrar</a></p>
        </section>
    </main>

</body>
</html>
