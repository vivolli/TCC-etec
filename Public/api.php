<?php

header('Content-Type: application/json; charset=utf-8');

// Definir diretório raiz
define('ROOT_DIR', dirname(__DIR__));

// Incluir classes
require_once ROOT_DIR . '/app/Config/Database.php';
require_once ROOT_DIR . '/app/Models/LivroModel.php';
require_once ROOT_DIR . '/app/Controllers/CatalogoController.php';
require_once ROOT_DIR . '/app/Config/AuthCheck.php';

use App\Controllers\CatalogoController;
use App\Config\Database;
use App\Config\AuthCheck;

try {
    $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Endpoint de verificação de autenticação
    if (strpos($request, '/TCC-etec/api/auth/check') !== false) {
        $usuario = AuthCheck::obterUsuarioLogado();
        
        if ($usuario) {
            echo json_encode([
                'autenticado' => true,
                'usuario' => $usuario
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(401);
            echo json_encode([
                'autenticado' => false,
                'mensagem' => 'Não autenticado'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    if (strpos($request, '/TCC-etec/api/livros/') !== false && preg_match('/\/TCC-etec\/api\/livros\/(\d+)/', $request, $matches)) {
        $livroId = (int)$matches[1];
        $controller = new CatalogoController();
        $livro = $controller->obterDetalhe($livroId);
        
        if ($livro) {
            echo json_encode([
                'sucesso' => true,
                'dados' => $livro
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Livro não encontrado'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    if (strpos($request, '/TCC-etec/api/livros') !== false) {
        $controller = new CatalogoController();
        $resultado = $controller->listarLivros();
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (strpos($request, '/TCC-etec/api/catalogo/metadados') !== false) {
        $controller = new CatalogoController();
        $metadados = $controller->obterMetadadosCatalogo();
        echo json_encode([
            'sucesso' => true,
            'dados' => $metadados
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['sucesso' => false, 'mensagem' => 'Rota não encontrada'], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage(),
        'arquivo' => $e->getFile(),
        'linha' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
}
?>
