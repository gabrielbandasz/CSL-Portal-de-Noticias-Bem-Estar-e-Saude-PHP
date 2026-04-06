<?php
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $noticia = $_POST['noticia'] ?? '';
    $imagem = null;

    $erros = validar_formulario(['titulo' => $titulo, 'noticia' => $noticia]);

    if (!empty($_FILES['imagem']['name'])) {
        $resultado_upload = fazer_upload_imagem($_FILES['imagem']);
        if ($resultado_upload['sucesso']) {
            $imagem = $resultado_upload['caminho'];
        } else {
            $erros[] = $resultado_upload['mensagem'] ?? 'Erro no upload da imagem.';
        }
    }

    if (empty($erros)) {
        $resultado = criar_noticia($conexao, $titulo, $noticia, $_SESSION['usuario_id'], $imagem);
        if ($resultado['sucesso']) {
            $sucesso = $resultado['mensagem'] ?? 'Notícia publicada com sucesso!';
            echo '<script>setTimeout(() => { window.location.href = "dashboard.php"; }, 2000);</script>';
        } else {
            $erro = $resultado['mensagem'] ?? 'Erro ao publicar notícia.';
        }
    } else {
        $erro = implode('<br>', $erros);
    }
}

$usuario = obter_usuario($conexao, $_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Notícia - Saúde e Bem-Estar</title>
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
            <section class="form-section">
                <div class="form-container" style="max-width:680px;">
                    <h2>📰 Publicar Nova Notícia</h2>
                    <p class="form-descricao">Compartilhe suas notícias sobre saúde e bem-estar com nossa comunidade</p>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo $erro; ?></div>
                    <?php endif; ?>
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success">✅ <?php echo $sucesso; ?> Redirecionando para seu dashboard...</div>
                    <?php endif; ?>

                    <form method="POST" action="nova_noticia.php" class="form" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="titulo">Título da Notícia</label>
                            <input type="text" id="titulo" name="titulo" required
                                value="<?php echo sanitizar($_POST['titulo'] ?? ''); ?>"
                                placeholder="Ex: Benefícios da Meditação para a Saúde Mental">
                        </div>

                        <div class="form-group">
                            <label for="noticia">Conteúdo da Notícia</label>
                            <textarea id="noticia" name="noticia" required rows="12"
                                placeholder="Digite o conteúdo completo da sua notícia..."><?php echo sanitizar($_POST['noticia'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="imagem">Imagem (opcional)</label>
                            <div class="upload-area">
                                <input type="file" id="imagem" name="imagem" accept="image/*">
                            </div>
                            <small>Formatos aceitos: JPG, PNG, GIF, WEBP. Tamanho máximo: 5MB</small>
                        </div>

                        <div class="form-acoes">
                            <button type="submit" class="btn-primary btn-large">Publicar Notícia</button>
                            <a href="dashboard.php" class="btn-secondary">Cancelar</a>
                        </div>
                    </form>
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
