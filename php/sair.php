<?php
require_once __DIR__ . '/autenticacao.php';

encerrar_sessao();

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
setcookie('remember_me', '', time() - 3600, '/', '', $secure, true);

header('Location: /TCC-etec/php/login/login.html?success=' . urlencode('Você saiu com sucesso.'));
exit;
