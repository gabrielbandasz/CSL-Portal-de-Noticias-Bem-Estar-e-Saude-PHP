<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$erro  = '';
$aviso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $erros = validar_formulario(['email' => $email, 'senha' => $senha]);

    if (empty($erros)) {
        $resultado = fazer_login($conexao, $email, $senha);
        if ($resultado === true) {
            $redirect = $_GET['redirect'] ?? (usuario_adm() ? 'admin.php' : 'dashboard.php');
            header("Location: " . $redirect);
            exit();
        } elseif ($resultado === 'aguardando') {
            $aviso = "Sua conta ainda não foi aprovada pelo administrador. Aguarde a liberação do acesso.";
        } else {
            $erro = "Email ou senha incorretos";
        }
    } else {
        $erro = implode('<br>', $erros);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Saúde e Bem-Estar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo"><h1>Saúde & Bem-Estar</h1></div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Início</a>
                <a href="cadastro.php" class="btn-primary btn-small">Cadastro</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <section class="form-section">
                <div class="form-container">
                    <h2>Login</h2>
                    <p class="form-descricao">Acesse sua conta para gerenciar suas notícias</p>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo $erro; ?></div>
                    <?php endif; ?>
                    <?php if ($aviso): ?>
                        <div class="alert alert-warning">⏳ <?php echo $aviso; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="login.php" class="form">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required
                                value="<?php echo sanitizar($_POST['email'] ?? ''); ?>"
                                placeholder="seu@email.com">
                        </div>
                        <div class="form-group">
                            <label for="senha">Senha</label>
                            <input type="password" id="senha" name="senha" required
                                placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn-primary btn-large">Entrar</button>
                    </form>

                    <p class="form-link">Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Portal de Notícias - Saúde e Bem-Estar. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
