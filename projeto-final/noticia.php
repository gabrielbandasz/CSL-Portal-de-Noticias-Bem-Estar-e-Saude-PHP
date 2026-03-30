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
    <title><?php echo sanitizar($noticia['titulo']); ?></title>
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
            <article class="noticia-completa">

                <?php if ($noticia['imagem']): ?>
                    <img src="<?php echo sanitizar($noticia['imagem']); ?>" class="noticia-imagem-grande">
                <?php else: ?>
                    <div class="noticia-imagem-placeholder-grande">📰</div>
                <?php endif; ?>

                <h1><?php echo sanitizar($noticia['titulo']); ?></h1>

                <!-- AUTOR COM FOTO -->

                <div class="noticia-info">

                    <div class="autor-box">
                        <?php if (!empty($noticia['foto_autor'])): ?>
                            <img src="<?php echo sanitizar($noticia['foto_autor']); ?>"
                                style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                        <?php else: ?>
                            <div class="foto-autor placeholder">👤</div>
                        <?php endif; ?>

                        <span><?php echo sanitizar($noticia['nome_autor']); ?></span>
                    </div>

                    <span class="data">
                        📅 <?php echo formatar_data($noticia['data']); ?>
                    </span>

                </div>

                <div class="noticia-texto">
                    <?php echo nl2br(sanitizar($noticia['noticia'])); ?>
                </div>

                <a href="index.php" class="btn-secondary">← Voltar</a>
        </div>

        </article>
        </div>
    </main>

</body>

</html>