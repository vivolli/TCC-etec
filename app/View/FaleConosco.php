<!DOCTYPE html>
<html>
<head>
    <title>FETEL - Fale Conosco</title>
    <link rel="stylesheet" href="../css/faleconosco.css">
</head>
<body>

<div class="sidebar">

    <a class="brand-link" href="index.html" aria-label="FETEL — Início">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="2" width="20" height="20" rx="4" fill="#0056b3"/>
            <path d="M6 16V8h3l3 6V8h3v8h-3l-3-6v6H6z" fill="#fff"/>
        </svg>
        <span class="brand">FETEL</span>
    </a>

    <ul>
        <li><a href="noticias.php">Notícias</a></li>
        <li><a href="usuarios.php">Usuários</a></li>
        <li><a href="configuracoes.php">Configurações</a></li>
    </ul>

</div>

<div class="main">

    <div class="header">
        <h1>Fale Conosco</h1>
        <div>Central de Atendimento</div>
    </div>

    <div class="card">

        <h2>Envie sua solicitação</h2>

        <?php if (!empty($resultado)): ?>
            <div class="mensagem">
                <?php echo $resultado; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-field">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-field">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-field">
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" required>
                    <option value="duvida">Dúvida</option>
                    <option value="reclamacao">Reclamação</option>
                    <option value="elogio">Elogio</option>
                    <option value="login">Problema com Login</option>
                    <option value="dados">Dados Pessoais</option>
                </select>
            </div>

            <div class="form-field">
                <label for="mensagem">Mensagem:</label>
                <textarea id="mensagem" name="mensagem" rows="5" required></textarea>
            </div>

            <button type="submit">Enviar</button>

        </form>

    </div>

</div>

</body>
</html>