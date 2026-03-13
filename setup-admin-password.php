<?php
/**
 * setup-admin-password.php
 * Script para gerar hash bcrypt e atualizar a senha do admin
 * 
 * Execução: php setup-admin-password.php
 */

$password = 'admin';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "═══════════════════════════════════════════════════════════\n";
echo "  SETUP SENHA ADMIN - TCC ETEC\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "📝 Informações:\n";
echo "   Email: admin@fetel.edu.br\n";
echo "   Senha: $password\n";
echo "   Hash: $hash\n\n";

echo "💾 SQL para atualizar no banco:\n";
echo "───────────────────────────────────────────────────────────\n";
echo "UPDATE usuarios SET senha_hash = '$hash' WHERE email = 'admin@fetel.edu.br';\n";
echo "───────────────────────────────────────────────────────────\n\n";

echo "📝 Passos:\n";
echo "   1. Abra phpMyAdmin em http://localhost/phpmyadmin\n";
echo "   2. Selecione banco 'tcc_etec'\n";
echo "   3. Vá na aba SQL\n";
echo "   4. Cole o comando acima\n";
echo "   5. Clique em 'Go'\n\n";

echo "✅ Depois faça login com:\n";
echo "   Email: admin@fetel.edu.br\n";
echo "   Senha: admin\n\n";

echo "🧪 Ou use PHP para fazer CLI:\n";
echo "───────────────────────────────────────────────────────────\n";
echo "php -r \"require 'Config/bootstrap.php'; \\\n";
echo "  \$db = Database::getInstance(); \\\n";
echo "  \$pdo = \$db->getConnection(); \\\n";
echo "  \$sql = 'UPDATE usuarios SET senha_hash = ? WHERE email = ?'; \\\n";
echo "  \$stmt = \$pdo->prepare(\$sql); \\\n";
echo "  \$stmt->execute(['$hash', 'admin@fetel.edu.br']); \\\n";
echo "  echo 'Senha atualizada!';\"\n";
echo "───────────────────────────────────────────────────────────\n\n";

echo "═══════════════════════════════════════════════════════════\n";
echo "✨ Pronto!\n";
echo "═══════════════════════════════════════════════════════════\n";
?>
