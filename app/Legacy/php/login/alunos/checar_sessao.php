<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

require_once __DIR__ . '/../_sessao.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

$info = getSessaoInfo();
$papel = strtolower((string)($info['papel'] ?? ''));
$logado = ($papel === 'aluno') && ($info['logado'] ?? false);

echo json_encode(['logado' => (bool)$logado], JSON_UNESCAPED_UNICODE);
exit;


