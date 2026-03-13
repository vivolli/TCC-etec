<?php
/**
 * Public/login.php
 * Página de login do sistema com autenticação via banco de dados
 */

declare(strict_types=1);

session_start();

// Carrega bootstrap com autoload
require_once __DIR__ . '/../Config/bootstrap.php';

use App\Model\Usuario as UsuarioModel;

// Se já está logado, redireciona para a página apropriada
if (esta_logado()) {
    $papel = strtolower((string)($_SESSION['usuario_papel'] ?? ''));
    if (in_array($papel, ['admin', 'adm', 'administrador', 'professor', 'prof', 'docente'], true)) {
        header('Location: /TCC-etec/Public/admin.php');
    } elseif (in_array($papel, ['secretaria', 'secretário', 'secretariao', 'secretaria_adj', 'funcionario'], true)) {
        header('Location: /TCC-etec/Public/secretaria.php');
    } else {
        header('Location: /TCC-etec/Public/aluno.php');
    }
    exit;
}

$erro = '';
$mensagem = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // Validação básica
    if (empty($email) || empty($senha)) {
        $erro = 'Email e senha são obrigatórios.';
    } else {
        try {
            $db = Database::getInstance();
            $modeloUsuario = new UsuarioModel($db->getConnection());

            // Busca o usuário por email
            $usuario = $modeloUsuario->buscarPorEmail($email);

            if (!$usuario) {
                $erro = 'Email ou senha incorretos.';
                // Registra tentativa para email que não existe (contra força bruta)
                $modeloUsuario->registrarTentativa(0);
            } else {
                // Verifica se está bloqueado por muitas tentativas
                if ($modeloUsuario->estaBloqueado((int)$usuario['id'])) {
                    $erro = 'Conta temporariamente bloqueada. Tente novamente em alguns minutos.';
                } else {
                    // Valida a senha
                    if ($modeloUsuario->validarSenha($senha, $usuario['senha_hash'])) {
                        // Limpa tentativas de login
                        $modeloUsuario->limparTentativas((int)$usuario['id']);

                        // Inicia sessão
                        iniciar_sessao_segura();
                        $_SESSION['usuario_id'] = (int)$usuario['id'];
                        $_SESSION['usuario_email'] = $usuario['email'];
                        $_SESSION['usuario_nome'] = $usuario['nome_completo'];
                        $_SESSION['usuario_papel'] = $usuario['papel'];

                        // Registra na auditoria
                        $modeloUsuario->registrarAuditoria((int)$usuario['id'], 'login_realizado', [
                            'ip' => $_SERVER['REMOTE_ADDR'],
                            'user_agent' => $_SERVER['HTTP_USER_AGENT']
                        ]);

                        // Redireciona baseado no papel
                        if (in_array(strtolower($usuario['papel']), ['admin', 'adm', 'administrador', 'professor', 'prof', 'docente'], true)) {
                            header('Location: /TCC-etec/Public/admin.php');
                        } elseif (in_array(strtolower($usuario['papel']), ['secretaria', 'secretário', 'secretariao', 'secretaria_adj', 'funcionario'], true)) {
                            header('Location: /TCC-etec/Public/secretaria.php');
                        } else {
                            header('Location: /TCC-etec/Public/aluno.php');
                        }
                        exit;
                    } else {
                        $erro = 'Email ou senha incorretos.';
                        $modeloUsuario->registrarTentativa((int)$usuario['id']);
                    }
                }
            }
        } catch (\Exception $e) {
            error_log('Erro no login: ' . $e->getMessage());
            $erro = 'Erro ao processar login. Tente novamente mais tarde.';
        }
    }
}

// Se vier do logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Refresh:0');
    exit;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Login — FETEL</title>
    <link rel="stylesheet" href="/TCC-etec/Public/css/login.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem;
        }
        .login-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-form h1 {
            text-align: center;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .login-form .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-form .logo img {
            height: 80px;
            width: auto;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
        }
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.9rem;
        }
        .login-footer a {
            color: #667eea;
            text-decoration: none;
        }
        .login-footer a:hover {
            text-decoration: underline;
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .remember-me input {
            width: auto;
            margin-right: 0.5rem;
        }
        .remember-me label {
            margin-bottom: 0;
            font-weight: normal;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <div class="logo">
                <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="48" stroke="#667eea" stroke-width="2"/>
                    <path d="M50 20C35.6 20 24 31.6 24 46C24 56.4 30 64.8 38 68C40 68.8 42 68 42 66V52H58V66C58 68 60 68.8 62 68C70 64.8 76 56.4 76 46C76 31.6 64.4 20 50 20Z" fill="#667eea"/>
                </svg>
            </div>

            <h1>FETEL</h1>
            <p style="text-align: center; color: #666; margin-bottom: 2rem;">Plataforma Escolar</p>

            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        autocomplete="username"
                        value="<?php echo htmlspecialchars($email); ?>"
                        placeholder="seu@email.com"
                    >
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••"
                    >
                </div>

                <div class="remember-me">
                    <input type="checkbox" id="lembrarme" name="lembrarme" value="1">
                    <label for="lembrarme">Lembrar-me deste dispositivo</label>
                </div>

                <button type="submit" class="btn-login">Entrar</button>
            </form>

            <div class="login-footer">
                <p><a href="#horario">Esqueceu sua senha?</a></p>
                <hr style="border: none; border-top: 1px solid #ddd; margin: 1rem 0;">
                <p>Ainda não tem conta? <a href="/TCC-etec/index.php">Voltar</a></p>
            </div>
        </div>
    </div>
</body>
</html>
