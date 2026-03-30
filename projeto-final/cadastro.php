<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    $erros = validar_formulario(['nome' => $nome, 'email' => $email, 'senha' => $senha]);
    
    if ($senha !== $confirmar_senha) {
        $erros[] = "As senhas não coincidem";
    }
    
    if (empty($erros)) {
        $resultado = criar_usuario($conexao, $nome, $email, $senha);
        if ($resultado['sucesso']) {
            $sucesso = $resultado['mensagem'];
            // Limpar formulário
            $_POST = [];
            echo '<script>setTimeout(() => { window.location.href = "login.php"; }, 2000);</script>';
        } else {
            $erro = $resultado['mensagem'];
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
    <title>Cadastro - Saúde e Bem-Estar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1>Saúde & Bem-Estar</h1>
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Início</a>
                <a href="login.php" class="nav-link btn-primary">Login</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <section class="form-section">
                <div class="form-container">
                    <h2>Criar Conta</h2>
                    <p class="form-descricao">Cadastre-se para publicar suas notícias sobre saúde e bem-estar</p>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger">
                            <?php echo $erro; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($sucesso): ?>
                        <div class="alert alert-success">
                            <?php echo $sucesso; ?> Redirecionando para login...
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="cadastro.php" class="form">
                        <div class="form-group">
                            <label for="nome">Nome Completo:</label>
                            <input type="text" id="nome" name="nome" required value="<?php echo sanitizar($_POST['nome'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required value="<?php echo sanitizar($_POST['email'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="senha">Senha:</label>
                            <input type="password" id="senha" name="senha" required>
                        </div>

                        <div class="form-group">
                            <label for="confirmar_senha">Confirmar Senha:</label>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                        </div>

                        <button type="submit" class="btn-primary btn-large">Cadastrar</button>
                    </form>

                    <p class="form-link">
                        Já tem uma conta? <a href="login.php">Faça login aqui</a>
                    </p>
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