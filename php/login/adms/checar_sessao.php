<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

require_once __DIR__ . '/../../autenticacao.php';

iniciar_sessao_segura();
$logado = false;
if (esta_logado()) {
    $papel = $_SESSION['usuario_papel'] ?? '';
    if (in_array($papel, ['adm','administrador'])) $logado = true;
}
echo json_encode(['logado' => $logado]);
exit;
