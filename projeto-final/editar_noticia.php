<?php
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: dashboard.php");
    exit();
}

$noticia = obter_noticia($conexao, $id);

if (!$noticia) {
    die("Erro: notícia não encontrada.");
}

if ($noticia['autor'] != $_SESSION['usuario_id']) {
    header("Location: dashboard.php");
    exit();
}

$usuario = obter_usuario($conexao, $_SESSION['usuario_id']);
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo   = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['noticia'] ?? '');
    $imagem   = $noticia['imagem'];

    $erros = validar_formulario(['titulo' => $titulo, 'noticia' => $conteudo]);

    if (isset($_FILES['imagem']) && !empty($_FILES['imagem']['name'])) {
        $upload = fazer_upload_imagem($_FILES['imagem']);
        if ($upload['sucesso']) {
            if (!empty($imagem) && file_exists($imagem)) {
                unlink($imagem);
            }
            $imagem = $upload['caminho'];
        } else {
            $erros[] = $upload['mensagem'];
        }
    }

    if (empty($erros)) {
        $resultado = atualizar_noticia($conexao, $id, $titulo, $conteudo, $imagem);
        if ($resultado['sucesso']) {
            header("Location: noticia.php?id=" . $id);
            exit();
        } else {
            $erro = $resultado['mensagem'];
        }
    } else {
        $erro = implode("<br>", $erros);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Notícia — Saúde & Bem-Estar</title>
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

<div class="editar-page">
    <div class="container">
        <div class="editar-card">

            <div class="editar-card-header">
                <h2>Editar Notícia</h2>
                <p>Atualize o conteúdo da sua publicação</p>
            </div>

            <div class="editar-card-body">

                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?php echo $erro; ?></div>
                <?php endif; ?>
                <?php if ($sucesso): ?>
                    <div class="alert alert-success">✅ <?php echo $sucesso; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="form">

                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="titulo"
                            value="<?php echo sanitizar($noticia['titulo']); ?>"
                            placeholder="Título da notícia"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Conteúdo</label>
                        <textarea name="noticia" rows="12" required
                            placeholder="Escreva o conteúdo da notícia..."><?php echo sanitizar($noticia['noticia']); ?></textarea>
                    </div>

                    <?php if (!empty($noticia['imagem'])): ?>
                        <div class="form-group">
                            <label>Imagem atual</label>
                            <div class="imagem-atual-preview">
                                <img src="<?php echo sanitizar($noticia['imagem']); ?>"
                                     alt="Imagem atual da notícia">
                                <div class="imagem-atual-info">
                                    <span>Imagem em uso</span>
                                    <small>Faça upload abaixo para substituir</small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Nova imagem <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--cor-texto-subtil);">(opcional)</span></label>
                        <div class="upload-area">
                            <input type="file" name="imagem" accept="image/*">
                        </div>
                        <small>Formatos aceitos: JPG, PNG, GIF, WEBP</small>
                    </div>

                    <div class="editar-acoes">
                        <button type="submit" class="btn-primary">Salvar alterações</button>
                        <a href="dashboard.php" class="btn-secondary">Cancelar</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p>&copy; 2026 Portal de Notícias - Saúde e Bem-Estar. Todos os direitos reservados.</p>
    </div>
</footer>

</body>
</html>
