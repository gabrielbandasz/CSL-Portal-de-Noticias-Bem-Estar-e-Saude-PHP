<?php
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

$usuario_id = $_SESSION['usuario_id'];
$usuario = obter_usuario($conexao, $usuario_id);

$mensagem = "";
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';

    $erros = validar_formulario($_POST);

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
    <title>Editar Perfil - Saúde e Bem-Estar</title>
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
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <?php if (usuario_adm()): ?>
                    <a href="admin.php" class="nav-link-adm">👑 Painel ADM</a>
                <?php endif; ?>
                <span class="user-info">
                    <?php if (!empty($usuario['foto'])): ?>
                        <img src="<?php echo sanitizar($usuario['foto']); ?>"
                            style="width:26px; height:26px; border-radius:50%; object-fit:cover;">
                    <?php endif; ?>
                    Olá, <?php echo sanitizar($_SESSION['usuario_nome']); ?>
                </span>
                <a href="nova_noticia.php" class="btn-primary btn-small">+ Nova Notícia</a>
                <a href="logout.php" class="btn-danger btn-small">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="form-container-usu">

                <h2>Editar Perfil</h2>
                <p class="form-descricao">Atualize suas informações pessoais e foto de perfil</p>

                <div class="foto-perfil-preview">
                    <?php if (!empty($usuario['foto'])): ?>
                        <img src="<?php echo sanitizar($usuario['foto']); ?>" alt="Foto de perfil">
                    <?php else: ?>
                        <div class="foto-perfil-placeholder">👤</div>
                    <?php endif; ?>
                    <div class="foto-perfil-info">
                        <strong><?php echo sanitizar($usuario['nome']); ?></strong>
                        <small>Foto de perfil atual</small>
                    </div>
                </div>

                <?php if (!empty($mensagem)): ?>
                    <div class="sucesso">✅ <?php echo $mensagem; ?></div>
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

                <form method="POST" enctype="multipart/form-data" class="form">

                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome"
                            value="<?php echo sanitizar($usuario['nome']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email"
                            value="<?php echo sanitizar($usuario['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Foto de Perfil <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--cor-texto-subtil);">(opcional)</span></label>
                        <div class="upload-area">
                            <input type="file" name="foto" accept="image/*">
                        </div>
                        <small>Formatos aceitos: JPG, PNG, GIF, WEBP</small>
                    </div>

                    <div class="form-acoes">
                        <button type="submit" class="btn-primary btn-large">Salvar Alterações</button>
                        <a href="dashboard.php" class="btn-secondary">Cancelar</a>
                    </div>

                </form>

            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Portal de Notícias - Saúde e Bem-Estar. Todos os direitos reservados.</p>
        </div>
    </footer>

</body>
</html>
