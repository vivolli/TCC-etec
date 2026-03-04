<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

require_once __DIR__ . '/../autenticacao.php';

$logado = esta_logado() ? true : false;
echo json_encode(['logado' => $logado]);
exit;
