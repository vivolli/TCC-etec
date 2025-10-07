<?php
require_once __DIR__ . '/../header.php';

$errors = [];
$success = false;

// provisorio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic sanitization
    $nome = isset($_POST['nome']) ? trim(strip_tags($_POST['nome'])) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mensagem = isset($_POST['mensagem']) ? trim(strip_tags($_POST['mensagem'])) : '';

    if ($nome === '') $errors[] = 'Informe seu nome.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Informe um e-mail válido.';
    if ($mensagem === '') $errors[] = 'Escreva sua mensagem.';

    if (empty($errors)) {
        $to = 'contato@fetel.edu.br'; // ajuste conforme necessário
        $subject = "[Fale Conosco] Nova mensagem de $nome";
        $body = "Nome: $nome\nEmail: $email\n\nMensagem:\n$mensagem\n";
        $headers = "From: $nome <$email>\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\n";

        // try to send email; if mail() not configured, fallback to log file
        $mailSent = false;
        if (function_exists('mail')) {
            $mailSent = @mail($to, $subject, $body, $headers);
        }

        if ($mailSent) {
            $success = true;
        } else {
            // fallback: append to log file with timestamp
            $logLine = sprintf("%s | %s | %s | %s\n", date('c'), $nome, $email, str_replace("\n", "\\n", $mensagem));
            $logFile = __DIR__ . '/contatos.log';
            if (@file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX) !== false) {
                $success = true;
            } else {
                $errors[] = 'Não foi possível enviar a mensagem no momento. Tente novamente mais tarde.';
            }
        }
    }
} else {
    $errors[] = 'Requisição inválida.';
}

?>

<main>
  <section class="section">
    <div class="container section-inner">
      <div class="section-header">
        <h2>Fale Conosco</h2>
      </div>

      <div class="contact-wrap">
        <?php if (!empty($errors)): ?>
          <div class="panel" style="border-left:4px solid #d9534f;background:#fff7f7;padding:12px;margin-bottom:12px">
            <strong>Ocorreram erros:</strong>
            <ul>
              <?php foreach ($errors as $err): ?>
                <li><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="panel" style="border-left:4px solid #28a745;background:#f7fff7;padding:12px;margin-bottom:12px">
            <strong>Obrigado!</strong>
            <p>Sua mensagem foi recebida. Entraremos em contato em breve.</p>
            <p><a href="index.html" class="btn">Voltar</a></p>
          </div>
        <?php else: ?>
          <p><a href="/TCC/php/fale-conosco.php" class="btn">Voltar ao formulário</a></p>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div class="contacts">
      <h4>Contatos</h4>
      <p>Rua Exemplo, 123 — Centro, São Paulo, SP</p>
      <p>Telefone: (11) 4000-0000 | E-mail: contato@fetel.edu.br</p>
    </div>
  </div>
  <div class="container copyright">
    <p>© <?php echo date('Y'); ?> FETEL — Todos os direitos reservados.</p>
  </div>
</footer>

<script src="/TCC/js/script.js" defer></script>
