# Guia de Implementação - Login e Modelos de Banco de Dados

Implementação concluída de login com autenticação, conexão com o banco de dados e autoloader Composer.

## 📋 O que foi Feito

### 1. **Lógica Real de Login** (Public/login.php)
- Validação de email e senha contra o banco de dados
- Hash seguro de senhas com bcrypt
- Limitação de tentativas de login (bloqueio após 5 tentativas)
- Registro de auditoria de logins
- Redirecionamento automático baseado no papel (aluno/funcionário/admin)
- Interface responsiva e segura

**Credenciais de Teste:**
```
Email: admin@fetel.edu.br
Senha: (verifique o hash no banco de dados)

Email: testedeveloper07@gmail.com
Papel: aluno
```

### 2. **Autoloader Composer** (composer.json)
O composer.json foi atualizado com:
```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "App\\Model\\": "app/Model/",
        "App\\Controller\\": "app/Controller/",
        "App\\View\\": "app/View/",
        "Core\\": "Core/"
    },
    "files": [
        "Config/db/conexao.php",
        "Core/autenticacao.php"
    ]
}
```

**Para usar:**
```bash
cd /path/to/TCC-etec
composer dump-autoload
```

Depois importar as classes:
```php
use App\Model\Usuario;
use App\Model\Aluno;
use App\Model\Funcionario;
```

### 3. **Novos Modelos (Classes)** criados:

#### **app/Model/Usuario.php**
Gerencia dados de usuários:
- `buscarPorEmail(email)` - Busca usuário por email
- `buscarCompleto(id)` - Retorna dados completos com relacionamentos
- `validarSenha(senha, hash)` - Valida senha
- `gerarHashSenha(senha)` - Gera hash seguro
- `registrarTentativa(usuarioId)` - Registra tentativa de login
- `estaBloqueado(usuarioId)` - Verifica se está bloqueado
- `registrarAuditoria(usuarioId, acao, meta)` - Registra ações
- `buscarNoticias(limite)` - Busca notícias publicadas

#### **app/Model/Aluno.php**
Gerencia dados de alunos:
- `buscarCompleto(usuarioId)` - Dados completos do aluno
- `buscarEmprestimosAtivos(usuarioId)` - Empréstimos em andamento
- `buscarHistoricoEmprestimos(usuarioId)` - Histórico completo
- `buscarTurmas(usuarioId)` - Turmas do aluno
- `buscarSolicitacoes(usuarioId)` - Solicitações de secretaria
- `buscarLivrosDisponiveis(limite)` - Livros na biblioteca
- `criarSolicitacao(usuarioId, tipo, detalhes)` - Nova solicitação

#### **app/Model/Funcionario.php**
Gerencia dados de funcionários/secretários:
- `buscarCompleto(usuarioId)` - Dados do funcionário
- `buscarSolicitacoes(limite, offset)` - Solicitações abertas
- `buscarSolicitacoesPorStatus(status, limite)` - Por status
- `atualizarStatusSolicitacao(id, status)` - Atualiza status
- `buscarEmprestimosAtrasados(limite)` - Empréstimos vencidos
- `obterEstatisticasBiblioteca()` - Stats da biblioteca
- `listarAlunos(limite, offset)` - Lista alunos

### 4. **Páginas Conectadas ao Banco de Dados**

#### **Public/aluno.php**
Dashboard do aluno com:
- ✅ Informações do perfil
- ✅ Empréstimos ativos
- ✅ Última notícias da escola
- ✅ Status das solicitações de secretaria
- ✅ Navegação para biblioteca e catálogo

#### **Public/secretaria.php**
Dashboard de secretária com:
- ✅ Estatísticas da biblioteca
- ✅ Solicitações abertas
- ✅ Empréstimos atrasados
- ✅ Prioridades de atendimento
- ✅ Ações rápidas

### 5. **Funções Auxiliares** (_sessao.php)
Novas funções para trabalhar com sessão:
- `getSessaoInfo()` - Retorna dados da sessão atual
- `requer_papel(papeis)` - Valida papel do usuário
- `eh_admin()` - Verifica se é admin
- `eh_professor()` - Verifica se é professor
- `eh_aluno()` - Verifica se é aluno
- `eh_funcionario()` - Verifica se é funcionário

---

## 🔐 Segurança Implementada

1. **Validação de Sessão**
   - Inicialização segura com flags HTTP-only
   - SameSite=Lax para proteção contra CSRF

2. **Proteção de Senhas**
   - Hash bcrypt com custo 12
   - Validação com password_verify()

3. **Limite de Tentativas**
   - Máx 5 tentativas por dia
   - Bloqueio automático da conta

4. **Auditoria**
   - Registro de todos os logins
   - IP e User-Agent armazenados

5. **SQL Injection**
   - Prepared Statements em todas as queries
   - Parâmetros vinculados corretamente

---

## 📊 Fluxo de Autenticação

```
┌────────────────────────────────────────────┐
│         User acessa /Public/login.php      │
└────────────────────────────────────────────┘
                    ↓
        ┌───────────────────────┐
        │ POST email + senha    │
        └───────────────────────┘
                    ↓
┌────────────────────────────────────────────┐
│ Busca usuário por email no BD              │
│ - Se não encontrado → erro + registra      │
│ - Se bloqueado → erro + bloqueia           │
│ - Se encontrado → valida senha             │
└────────────────────────────────────────────┘
                    ↓
        ┌───────────────────────┐
        │ password_verify()     │
        │ - Senha válida?       │
        └───────────────────────┘
                    ↓
    ┌─────────────────────────────────┐
    │ SIM: Cria sessão + registra log │
    │ NÃO: Incrementa tentativas      │
    └─────────────────────────────────┘
                    ↓
    ┌─────────────────────────────────┐
    │ Redireciona baseado no papel:  │
    │ - admin/prof → admin.php        │
    │ - aluno → aluno.php             │
    │ - funcionario → secretaria.php   │
    └─────────────────────────────────┘
```

---

## 🚀 Como Usar as Classes

### Exemplo 1: Buscar informações do usuário logado
```php
require_once __DIR__ . '/../Config/db/conexao.php';
use App\Model\Usuario;

$db = Database::getInstance();
$user = new Usuario($db->getConnection());

$info = getSessaoInfo();
$dados = $user->buscarCompleto($info['usuario_id']);

echo $dados['nome_completo'];
```

### Exemplo 2: Listar empréstimos do aluno
```php
require_once __DIR__ . '/../Config/db/conexao.php';
use App\Model\Aluno;

$db = Database::getInstance();
$aluno = new Aluno($db->getConnection());

$emprestimos = $aluno->buscarEmprestimosAtivos($usuario_id);

foreach ($emprestimos as $emp) {
    echo $emp['titulo'] . " - Vence em: " . $emp['vencimento_em'];
}
```

### Exemplo 3: Obter estatísticas na secretaria
```php
require_once __DIR__ . '/../Config/db/conexao.php';
use App\Model\Funcionario;

$db = Database::getInstance();
$func = new Funcionario($db->getConnection());

$stats = $func->obterEstatisticasBiblioteca();

echo "Total de livros: " . $stats['livros']['total'];
echo "Empréstimos atrasados: " . $stats['emprestimos_atrasados'];
```

---

## 📝 Próximos Passos

1. **Implementar Controllers** para lógica mais complexa
2. **Criar formulários** para criar solicitações de secretaria
3. **Implementar API** para operações de empréstimo
4. **Adicionar recuperação de senha** com token
5. **Criar dashboard do admin** com gerenciamento completo

---

## 🔧 Troubleshooting

### Erro: "Class not found: App\Model\Usuario"
**Solução:** Execute `composer dump-autoload` na raiz do projeto

### Erro: "Database connection failed"
**Solução:** Verifique arquivo `.env` com credenciais do banco

### Erro: "FATAL: allow_url_fopen is disabled"
**Solução:** Habite `allow_url_fopen=On` no php.ini

### Login não funciona
**Solução:** Verifique se a tabela `usuarios` existe e tem dados

---

Gerado em: 12/03/2026
Versão: 1.0
