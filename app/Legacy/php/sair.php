<?php
// canonical copy of php/sair.php
// prefer app bootstrap if available
@include_once __DIR__ . '/../../app/Core/Bootstrap.php';

require_once __DIR__ . '/../autenticacao.php';

encerrar_sessao();

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
setcookie('remember_me', '', time() - 3600, '/', '', $secure, true);

header('Location: /TCC-etec/app/php/login/entrar.php?success=' . urlencode('Você saiu com sucesso.'));
exit;


