<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$id = $_GET['id'] ?? null;
$usuario = null;

if (usuario_logado()) {
    $usuario = obter_usuario($conexao, $_SESSION['usuario_id']);
}

if (!$id) {
    header("Location: index.php");
    exit();
}

$noticia = obter_noticia($conexao, $id);

if (!$noticia) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitizar($noticia['titulo']); ?> - Saúde e Bem-Estar</title>
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

                <?php if (usuario_logado()): ?>
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
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="cadastro.php" class="btn-primary btn-small">Cadastro</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <article class="noticia-completa">

                <?php if ($noticia['imagem']): ?>
                    <img src="<?php echo sanitizar($noticia['imagem']); ?>" class="noticia-imagem-grande" alt="<?php echo sanitizar($noticia['titulo']); ?>">
                <?php else: ?>
                    <div class="noticia-imagem-placeholder-grande">📰</div>
                <?php endif; ?>

                <h1><?php echo sanitizar($noticia['titulo']); ?></h1>

                <div class="noticia-info">
                    <div class="autor-box">
                        <?php if (!empty($noticia['foto_autor'])): ?>
                            <img src="<?php echo sanitizar($noticia['foto_autor']); ?>"
                                style="width:28px; height:28px; border-radius:50%; object-fit:cover;">
                        <?php else: ?>
                            <span>👤</span>
                        <?php endif; ?>
                        <span><?php echo sanitizar($noticia['nome_autor']); ?></span>
                    </div>
                    <span>📅 <?php echo formatar_data($noticia['data']); ?></span>
                </div>

                <div class="noticia-texto">
                    <?php echo nl2br(sanitizar($noticia['noticia'])); ?>
                </div>

                <div class="noticia-acoes">
                    <a href="index.php" class="btn-secondary">← Voltar</a>
                    <?php if (usuario_logado() && (usuario_adm() || $_SESSION['usuario_id'] == $noticia['autor'])): ?>
                        <a href="editar_noticia.php?id=<?php echo $noticia['id']; ?>" class="btn-primary btn-small">Editar</a>
                        <a href="excluir_noticia.php?id=<?php echo $noticia['id']; ?>" class="btn-danger btn-small"
                            onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">Excluir</a>
                    <?php endif; ?>
                </div>

            </article>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Portal de Notícias - Saúde e Bem-Estar. Todos os direitos reservados.</p>
        </div>
    </footer>

</body>
</html>
