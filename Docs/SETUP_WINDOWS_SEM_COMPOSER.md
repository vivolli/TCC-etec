# 🔧 Setup do Sistema - Windows sem Composer

**Status:** ✅ **AUTOLOAD GERADO COM SUCESSO**

---

## ✨ O Que Foi Feito

1. ✅ Script `generate-autoload.php` criado
2. ✅ Autoload PSR-4 gerado em `vendor/autoload.php`
3. ✅ Bootstrap centralizado em `Config/bootstrap.php`
4. ✅ Sistema pronto para usar **SEM PRECISAR DE COMPOSER**

---

## 🚀 Como Usar

### **Usar nos seus arquivos PHP:**

```php
<?php
// NO INÍCIO DE QUALQUER ARQUIVO
require_once __DIR__ . '/../Config/bootstrap.php';

// Agora você pode usar as classes
use App\Model\Usuario;
use App\Model\Aluno;
use App\Model\Funcionario;

// Exemplo:
$usuario = new Usuario();
$dados = $usuario->buscarPorEmail('test@email.com');
```

### **Ou mais simples ainda:**

```php
<?php
require_once __DIR__ . '/../Config/bootstrap.php';

// Classes carregadas automaticamente
$db = Database::getInstance();
$usuario = new \App\Model\Usuario($db->getConnection());
```

---

## 📂 Estrutura de Arquivos

```
TCC-etec/
├── vendor/
│   ├── autoload.php          ← USAR ESTE ARQUIVO
│   ├── composer/
│   │   ├── autoload_psr4.php
│   │   ├── autoload_files.php
│   │   ├── ClassLoader.php
│   │   └── ...
│   ├── phpdotenv/
│   └── ...
├── Config/
│   ├── bootstrap.php         ← CARREGADOR CENTRALIZADO
│   ├── db/
│   │   └── conexao.php
│   └── ...
├── Core/
│   ├── autenticacao.php
│   └── ...
├── app/
│   ├── Model/
│   │   ├── Usuario.php
│   │   ├── Aluno.php
│   │   └── Funcionario.php
│   ├── Controller/
│   ├── View/
│   └── ...
├── Public/
│   ├── login.php             ← USA bootstrap.php
│   ├── aluno.php             ← USA bootstrap.php
│   ├── secretaria.php        ← USA bootstrap.php
└── ...
```

---

## 🔄 Se Precisar Regenerar Autoload

Execute no Windows PowerShell:

```powershell
cd C:\wamp64\www\TCC-etec
C:\wamp64\bin\php\php8.1.31\php.exe generate-autoload.php
```

Ou crie um atalho `.bat`:

```batch
@echo off
C:\wamp64\bin\php\php8.1.31\php.exe %~dp0generate-autoload.php
pause
```

---

## ✅ Testar o Sistema

### **1. Acesse a página de login:**
```
http://localhost/TCC-etec/Public/login.php
```

### **2. Use as credenciais de teste:**
```
Email: admin@fetel.edu.br
Ou
Email: testedeveloper07@gmail.com
```

### **3. Verifique os dashboards:**
- Admin → http://localhost/TCC-etec/Public/admin.php
- Aluno → http://localhost/TCC-etec/Public/aluno.php
- Secretária → http://localhost/TCC-etec/Public/secretaria.php

---

## 📝 Resumo das Classes Disponíveis

### **App\Model\Usuario**
```php
$usuario = new \App\Model\Usuario($pdo);

$usuario->buscarPorEmail('test@email.com');
$usuario->buscarCompleto(5);
$usuario->validarSenha($senha, $hash);
$usuario->registrarTentativa($usuarioId);
$usuario->estaBloqueado($usuarioId);
$usuario->registrarAuditoria($usuarioId, 'acao', ['dados']);
$usuario->buscarNoticias(10);
```

### **App\Model\Aluno**
```php
$aluno = new \App\Model\Aluno($pdo);

$aluno->buscarCompleto($usuarioId);
$aluno->buscarEmprestimosAtivos($usuarioId);
$aluno->buscarHistoricoEmprestimos($usuarioId);
$aluno->buscarTurmas($usuarioId);
$aluno->buscarSolicitacoes($usuarioId);
$aluno->buscarLivrosDisponiveis(20);
$aluno->criarSolicitacao($usuarioId, $tipo, $detalhes);
```

### **App\Model\Funcionario**
```php
$func = new \App\Model\Funcionario($pdo);

$func->buscarCompleto($usuarioId);
$func->buscarSolicitacoes(50, 0);
$func->buscarSolicitacoesPorStatus('aberto');
$func->atualizarStatusSolicitacao($id, 'em_andamento');
$func->buscarEmprestimosAtrasados(10);
$func->obterEstatisticasBiblioteca();
$func->listarAlunos(50, 0);
```

---

## 🔧 Troubleshooting

### **"Call to undefined class App\Model\Usuario"**
- Solução: Verifique se o arquivo existe em `app/Model/Usuario.php`
- Execute novamente: `php generate-autoload.php`

### **"Database::getInstance() not found"**
- Solução: Certifique-se que `Config/db/conexao.php` está incluído no `bootstrap.php`
- Verifique se o arquivo existe

### **"Fatal error: Uncaught exception"**
- Solução: Verifique as credenciais do banco de dados no `.env`
- Verifique o arquivo de log do PHP

---

## 💡 Exemplos de Uso

### **Exemplo 1: Buscar usuário logado**
```php
<?php
require_once __DIR__ . '/../Config/bootstrap.php';

$info = getSessaoInfo();
$db = Database::getInstance();
$usuario = new \App\Model\Usuario($db->getConnection());

$dados = $usuario->buscarCompleto($info['usuario_id']);
echo "Olá, " . $dados['nome_completo'];
```

### **Exemplo 2: Listar empréstimos**
```php
<?php
require_once __DIR__ . '/../Config/bootstrap.php';

$db = Database::getInstance();
$aluno = new \App\Model\Aluno($db->getConnection());

$emprestimos = $aluno->buscarEmprestimosAtivos(5);

foreach ($emprestimos as $emp) {
    echo $emp['titulo'] . " - Vence: " . $emp['vencimento_em'];
}
```

### **Exemplo 3: Verificar papel do usuário**
```php
<?php
require_once __DIR__ . '/../Config/bootstrap.php';

if (eh_admin()) {
    echo "Você é administrador";
} elseif (eh_aluno()) {
    echo "Você é aluno";
} elseif (eh_funcionario()) {
    echo "Você é funcionário";
}
```

---

## 📦 Próximos Passos

1. **Instalar Composer** (opcional, para produção):
   - Download: https://getcomposer.org/download/
   - Execute: `composer dump-autoload`

2. **Atualizar aplicação** para usar o Composer:
   - Remova a chamada para `bootstrap.php`
   - Use `require 'vendor/autoload.php'`
   - Funciona igual!

3. **Fazer deploy** em produção:
   - Use o sistema atual (já funciona)
   - Ou atualize para versão com Composer

---

## ✨ Conclusão

O sistema está **totalmente funcional** sem Composer! 
- ✅ Autoloader PSR-4 funcionando
- ✅ Classes carregadas dinamicamente
- ✅ Bootstrap centralizado
- ✅ Pronto para produção

**Basta usar `require_once __DIR__ . '/../Config/bootstrap.php'` em qualquer página!**

---

Gerado em: 12/03/2026
Sistema: Windows 10/11 + WAMP64
PHP: 8.1.31
