<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
if (file_exists(__DIR__ . '/phpmailer/vendor/autoload.php')) {
    require_once __DIR__ . '/phpmailer/vendor/autoload.php';
}

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

            //Recipients
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
                // 'banner1' is the CID referenced in the HTML body
                $mail->addEmbeddedImage($imageAttachment, 'banner1', 'fetel.png');
                // place image at the end and limit size to ~300px
                $embeddedHtml = "<div style='text-align:center;margin-top:18px'><img src='cid:banner1' alt='FETEL' style='width:100%;max-width:300px;height:auto;border-radius:8px;display:block;margin:0 auto'></div>";
            }

                $mailBodyHtml = <<<HTML
                <div style="font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Arial; color:#0f172a">
                    <h2>Olá,</h2>
                    <h3>Recebemos uma solicitação para redefinir sua senha. Se você solicitou a redefinição, siga as instruções enviadas neste e-mail.</h3>
                    <p style="margin-top:18px">Atenciosamente,<br><strong>FETEL</strong></p>
                </div>
                HTML;

                $mail->Body = $mailBodyHtml . $embeddedHtml;
                $mail->AltBody = "Recebemos uma solicitação para redefinir sua senha. Se você solicitou a redefinição, siga as instruções enviadas neste e-mail.\n\nAtenciosamente, FETEL";

            $mail->send();
            // Redirect after successful POST to avoid duplicate sends on reload (Post/Redirect/Get)
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
    <link rel="stylesheet" href="../../css/login.css">
    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 14px 18px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(16,24,40,0.08);
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        }
        .toast.success { background: #f0fdf4; border-left: 4px solid #16a34a; color: #064e3b; }
        .toast.error { background: #fff1f2; border-left: 4px solid #dc2626; color: #7f1d1d; }
        .toast .close { margin-left: 8px; background: transparent; border: none; color: inherit; cursor: pointer; font-weight:600 }
        .toast.hide { opacity: 0; transform: translateY(-8px); transition: all .35s ease; }
    </style>
    <meta name="robots" content="noindex">
</head>
<body>
    <script>
        // Auto-hide toast after page load if present
        document.addEventListener('DOMContentLoaded', function(){
            const t = document.querySelector('.toast');
            if(!t) return;
            setTimeout(()=>{
                t.classList.add('hide');
                setTimeout(()=>t.remove(),400);
            }, 4500);
        });
    </script>
    <?php if ($sent): ?>
        <div class="toast success" role="status" aria-live="polite">
            <div>
                <strong>Email de recuperacao enviado com sucesso</strong>
                <div style="font-size:.95rem;margin-top:6px;color:rgba(0,0,0,0.7)">Verifique o e-mail informado para instruções.</div>
            </div>
            <button class="close" aria-label="Fechar" onclick="this.closest('.toast').remove()">✕</button>
        </div>
    <?php elseif ($error): ?>
        <div class="toast error" role="alert">
            <div>
                <strong>Erro ao enviar e-mail</strong>
                <div style="font-size:.95rem;margin-top:6px;color:rgba(0,0,0,0.7)"><?=htmlspecialchars($error)?></div>
            </div>
            <button class="close" aria-label="Fechar" onclick="this.closest('.toast').remove()">✕</button>
        </div>
    <?php endif; ?>
    <main class="login-page">
    <a class="brand-link" href="login.php" aria-label="Voltar ao login">
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
