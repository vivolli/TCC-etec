<<<<<<<< HEAD:app/View/FaleConosco.php
========
<?php
// View/controller hybrid for Fale Conosco under app/Views.
// It uses the small local classes in ./classes/ to process submissions.
require_once __DIR__ . '/classes/ClasseAbstrata.php';
require_once __DIR__ . '/classes/duvidasFrequentes.php';
require_once __DIR__ . '/classes/reclamacoes.php';
require_once __DIR__ . '/classes/problemaComLogin.php';
require_once __DIR__ . '/classes/DadosPessoais.php';

$resultado = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';

    switch ($tipo) {
        case "duvida":
            $contato = new Duvida($nome, $email, $mensagem);
            break;

        case "reclamacao":
            $contato = new Feedback($nome, $email, $mensagem, "Reclamação");
            break;

        case "elogio":
            $contato = new Feedback($nome, $email, $mensagem, "Elogio");
            break;

        case "login":
            $contato = new ProblemaComLogin($nome, $email, $mensagem);
            break;

        case "dados":
            $contato = new DadosPessoais($nome, $email, $mensagem);
            break;

        default:
            $contato = null;
    }

    if ($contato !== null) {
        $resultado = $contato->processar();
    }
}
?>

>>>>>>>> c20f15da362d705ddd5a772287b27b3ecff75f58:app/Views/FaleConosco/faleConosco.php
<!DOCTYPE html>
<html>
<head>
    <title>FETEL - Fale Conosco</title>
<<<<<<<< HEAD:app/View/FaleConosco.php
    <link rel="stylesheet" href="../css/faleconosco.css">
========
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <?php include __DIR__ . '/../partials/assets.php'; ?>
>>>>>>>> c20f15da362d705ddd5a772287b27b3ecff75f58:app/Views/FaleConosco/faleConosco.php
</head>
<body>

<div class="sidebar">
<<<<<<<< HEAD:app/View/FaleConosco.php

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
========
    <a class="brand-link" href="/TCC-etec/app/public/index.html" aria-label="FETEL — Início">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <rect x="2" y="2" width="20" height="20" rx="4" fill="#0056b3" />
                <path d="M6 16V8h3l3 6V8h3v8h-3l-3-6v6H6z" fill="#fff" />
            </svg>
            <span class="brand">FETEL</span>
        </a>
    <ul>
        <li><a href="/TCC-etec/Noticias/noticias.html">Notícias</a></li>
        <li><a href="#">Usuários</a></li>
        <li><a href="#">Configurações</a></li>
>>>>>>>> c20f15da362d705ddd5a772287b27b3ecff75f58:app/Views/FaleConosco/faleConosco.php
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

        <?php
        // generate CSRF token
        require_once __DIR__ . '/../../Core/Csrf.php';
        $csrf = \App\Core\Csrf::generateToken();
        ?>

        <form method="POST">
            <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES); ?>">

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
