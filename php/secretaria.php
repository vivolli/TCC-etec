<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Secretaria — FETEL</title>
    <meta name="description" content="Serviços da Secretaria da FETEL">
    <link rel="stylesheet" href="../css/style.css">
  </head>
  <body>
    <header class="site-header">
      <div class="container header-inner">
        <a class="logo" href="../index.html" aria-label="FETEL - Início">
          <svg width="42" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect x="2" y="2" width="20" height="20" rx="4" fill="#0056b3" />
            <path d="M6 16V8h3l3 6V8h3v8h-3l-3-6v6H6z" fill="#fff" />
          </svg>
          <span class="brand">FETEL</span>
        </a>

        <nav class="nav" id="main-nav">
          <ul class="nav-list">
            <li><a href="../index.html#servicos">Serviços</a></li>
            <li><a href="../php/secretaria.php" class="active-link">Secretaria</a></li>
            <li><a href="../index.html#contato">Fale Conosco</a></li>
            <li><a href="../index.html#biblioteca">Biblioteca</a></li>
            <li><a href="../index.html#sobre">Sobre a Escola</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <main>
      <section class="section" style="padding-top:40px;">
        <div class="container section-inner">
          <div class="section-header">
            <h2>Secretaria</h2>
            <p class="section-sub">Serviços acadêmicos, horários e documentação.</p>
          </div>

          <div class="grid">
            <div class="box">
              <h4>Atendimento</h4>
              <p>Atendimento presencial e online para matrículas, emissão de documentos e dúvidas acadêmicas.</p>
            </div>
            <div class="box">
              <h4>Horários</h4>
              <p>Consulte os horários das turmas e os períodos de atendimento da secretaria. Horário de funcionamento: Seg–Sex 09:00–17:00.</p>
            </div>
            <div class="box">
              <h4>Financeiro</h4>
              <p>Informações sobre mensalidades, bolsas e formas de pagamento. Para solicitar segunda via ou negociar, compareça à secretaria ou entre em contato por e-mail.</p>
            </div>
          </div>

          <div style="margin-top:18px;">
            <p>Para agendar atendimento, envie um e-mail para <a href="mailto:contato@fetel.edu.br">contato@fetel.edu.br</a> ou utilize o telefone (11) 4000-0000.</p>
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
            <a href="#" aria-label="Facebook" class="social-link">FB</a>
            <a href="#" aria-label="Instagram" class="social-link">IG</a>
            <a href="#" aria-label="LinkedIn" class="social-link">IN</a>
          </div>
        </div>
      </div>

      <div class="container copyright">
        <p>© <?php echo date('Y'); ?> FETEL — Todos os direitos reservados.</p>
      </div>
    </footer>

    <script src="../js/script.js" defer></script>
  </body>
</html>
<?php 

?>