<?php
/**
 * test-autoload.php
 * Testa se o autoload está funcionando corretamente
 */

print_r(__FILE__);
echo "\n\n";

echo "=== TESTE DE AUTOLOAD ===\n\n";

// Testa bootstrap
echo "1. Carregando bootstrap.php... ";
require_once __DIR__ . '/Config/bootstrap.php';
echo "✓ OK\n\n";

// Testa autoload manual
echo "2. Testando classe Database... ";
if (class_exists('Database')) {
    echo "✓ OK - Classe encontrada\n";
} else {
    echo "✗ ERRO - Classe não encontrada\n";
}

// Testa classes do Model
echo "3. Testando classe App\\Model\\Usuario... ";
if (class_exists('App\\Model\\Usuario')) {
    echo "✓ OK - Classe encontrada\n";
} else {
    echo "✗ ERRO - Classe não encontrada\n";
}

echo "4. Testando classe App\\Model\\Aluno... ";
if (class_exists('App\\Model\\Aluno')) {
    echo "✓ OK - Classe encontrada\n";
} else {
    echo "✗ ERRO - Classe não encontrada\n";
}

echo "5. Testando classe App\\Model\\Funcionario... ";
if (class_exists('App\\Model\\Funcionario')) {
    echo "✓ OK - Classe encontrada\n";
} else {
    echo "✗ ERRO - Classe não encontrada\n";
}

// Testa funções de sessão
echo "6. Testando função getSessaoInfo... ";
if (function_exists('getSessaoInfo')) {
    echo "✓ OK - Função encontrada\n";
} else {
    echo "✗ ERRO - Função não encontrada\n";
}

echo "7. Testando função eh_aluno... ";
if (function_exists('eh_aluno')) {
    echo "✓ OK - Função encontrada\n";
} else {
    echo "✗ ERRO - Função não encontrada\n";
}

echo "\n=== TESTE DE BANCO DE DADOS ===\n\n";

// Testa conexão com banco
echo "8. Testando conexão com banco de dados... ";
try {
    $db = Database::getInstance();
    echo "✓ OK - Conexão estabelecida\n";
    
    echo "9. Testando query simples... ";
    $result = $db->query("SELECT COUNT(*) as total FROM usuarios");
    echo "✓ OK - Total de usuários: " . ($result[0]['total'] ?? 'N/A') . "\n";
    
} catch (Exception $e) {
    echo "✗ ERRO - " . $e->getMessage() . "\n";
}

echo "\n=== ARQUIVOS CRIADOS ===\n\n";

$files = [
    'vendor/autoload.php',
    'vendor/composer/autoload_psr4.php',
    'vendor/composer/autoload_files.php',
    'vendor/composer/ClassLoader.php',
    'Config/bootstrap.php',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $size = filesize($path);
        echo "✓ {$file} ({$size} bytes)\n";
    } else {
        echo "✗ {$file} (NÃO ENCONTRADO)\n";
    }
}

echo "\n=== RESULTADO FINAL ===\n\n";
echo "✨ Se todos os testes acima mostram ✓, o sistema está pronto!\n";
echo "Você pode acessar: http://localhost/TCC-etec/Public/login.php\n";
