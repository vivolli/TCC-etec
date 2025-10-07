<?php
require_once __DIR__ . '/../header.php';

// initialize variables to avoid undefined notices
$errors = isset($errors) && is_array($errors) ? $errors : [];
$nome = isset($nome) ? $nome : '';
$email = isset($email) ? $email : '';
$mensagem = isset($mensagem) ? $mensagem : '';
?>

<main>
  <section class="hero">
    <div class="container hero-inner">
      <h1>Fale Conosco</h1>
      <p class="lead">Tem alguma dúvida? Envie sua mensagem e responderemos em breve.</p>
      <div class="hero-cta">
        <a class="btn ghost" href="/TCC/index.html">Voltar ao Início</a>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container section-inner">
      <div class="section-header">
        <h2>Contato</h2>
        <p class="section-sub">Preencha o formulário abaixo e entraremos em contato.</p>
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

        <form method="post" action="fale-conosco-enviar.php" class="contact-form">
          <div class="row">
            <input autofocus type="text" name="nome" placeholder="Seu nome" required value="<?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="email" name="email" placeholder="Seu e-mail" required value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
          </div>
          <textarea name="mensagem" placeholder="Sua mensagem" rows="6" required><?php echo htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); ?></textarea>
          <div class="form-actions">
            <button class="btn primary" type="submit">Enviar Mensagem</button>
          </div>
        </form>
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

    <div class="social">
      <h4>Siga-nos</h4>
      <div class="social-links">
        <a href="#" aria-label="Facebook" class="social-link">
          <svg viewBox="0 0 24 24"><path fill="#0056b3" d="M22 12a10 10 0 10-11.5 9.9v-7h-2v-3h2v-2.3c0-2 1.2-3.1 3-3.1.9 0 1.8.2 1.8.2v2h-1c-1 0-1.3.6-1.3 1.2V12h2.3l-.4 3h-1.9v7A10 10 0 0022 12z"/></svg>
        </a>
        <a href="#" aria-label="Instagram" class="social-link">
          <svg viewBox="0 0 24 24"><path fill="#0056b3" d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 6a4 4 0 100 8 4 4 0 000-8zM18 6a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
        </a>
        <a href="#" aria-label="LinkedIn" class="social-link">
          <svg viewBox="0 0 24 24"><path fill="#0056b3" d="M4 3a2 2 0 100 4 2 2 0 000-4zM3 8h2v11H3zM8 8h2v1.6c.3-.6 1-1.6 2.6-1.6 2.8 0 3.4 1.9 3.4 4.4V19h-2v-4c0-1-.1-2.2-1.3-2.2-1.3 0-1.5 1-1.5 2v4.2H8z"/></svg>
        </a>
      </div>
    </div>
  </div>

  <div class="container copyright">
    <p>© <?php echo date('Y'); ?> FETEL — Todos os direitos reservados.</p>
  </div>
</footer>

<script src="/TCC/js/script.js" defer></script>