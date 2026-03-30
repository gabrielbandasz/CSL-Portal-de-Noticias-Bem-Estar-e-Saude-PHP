<?php
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

$usuario_id = $_SESSION['usuario_id'];
$usuario = obter_usuario($conexao, $usuario_id);

$mensagem = "";
$erros = [];

// Se enviou o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validação
    $erros = validar_formulario($_POST);

    // Upload da foto
    $foto_caminho = null;

    if (!empty($_FILES['foto']['name'])) {
        $upload = fazer_upload_imagem($_FILES['foto']);

        if ($upload['sucesso']) {
            $foto_caminho = $upload['caminho'];
        } else {
            $erros[] = $upload['mensagem'];
        }
    }

    if (empty($erros)) {
        $resultado = atualizar_usuario($conexao, $usuario_id, $nome, $email, $foto_caminho);

        if ($resultado['sucesso']) {
            $mensagem = $resultado['mensagem'];
            $usuario = obter_usuario($conexao, $usuario_id);
        } else {
            $erros[] = $resultado['mensagem'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

  <header class="header">
        <div class="container">
            <div class="logo">
                <h1>Saúde & Bem-Estar</h1>
            </div>

            <nav class="nav">
                <a href="index.php" class="nav-link active">Início</a>

                <?php if (usuario_logado()): ?>
                    <span class="user-info">

                        <?php if (!empty($usuario['foto'])): ?>
                            <img src="<?php echo $usuario['foto']; ?>"
                                style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                        <?php endif; ?>

                        Olá, <?php echo sanitizar($_SESSION['usuario_nome']); ?>
                    </span>

                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="nova_noticia.php" class="nav-link btn-primary">+ Nova Notícia</a>
                    <a href="logout.php" class="nav-link btn-danger">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="cadastro.php" class="nav-link btn-primary">Cadastro</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

<main class="main-content">
    <div class="container">
        <div class="form-container-usu">

            <h2>Atualizar seus dados</h2>

            <!-- Foto atual -->
            <div style="margin-bottom: 15px;">
                <?php if (!empty($usuario['foto'])): ?>
                    <img src="<?php echo $usuario['foto']; ?>" 
                         style="width:100px; height:100px; border-radius:50%; object-fit:cover;">
                <?php else: ?>
                    <p>Sem foto de perfil</p>
                <?php endif; ?>
            </div>

            <!-- Mensagens -->
            <?php if (!empty($mensagem)): ?>
                <div class="sucesso"><?php echo $mensagem; ?></div>
            <?php endif; ?>

            <?php if (!empty($erros)): ?>
                <div class="erro">
                    <ul>
                        <?php foreach ($erros as $erro): ?>
                            <li><?php echo $erro; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" 
                        value="<?php echo sanitizar($usuario['nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" 
                        value="<?php echo sanitizar($usuario['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Foto de Perfil</label>
                    <input type="file" name="foto" accept="image/*">
                </div>
                <br>

                <button type="submit" class="btn-primary">Salvar Alterações</button>

            </form>

        </div>
    </div>
</main>

</body>
</html>