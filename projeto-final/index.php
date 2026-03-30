<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$noticia_por_pagina = 5;

$noticias = listar_noticias($conexao, $noticia_por_pagina, $pagina_atual);
$total_noticias = contar_noticias($conexao);
$total_paginas = ceil($total_noticias / $noticia_por_pagina);

// Pega usuário logado (pra foto no header)
$usuario = null;
if (usuario_logado()) {
    $usuario = obter_usuario($conexao, $_SESSION['usuario_id']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Notícias - Saúde e Bem-Estar</title>
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

    <!-- Conteúdo Principal -->
    <main class="main-content">
        <div class="container">
            <section class="hero">
                <h2>Últimas Notícias sobre Saúde e Bem-Estar</h2>
                <p>Fique informado com as melhores notícias sobre saúde, nutrição, fitness e qualidade de vida.</p>
            </section>

            <section class="noticias-lista">
                <?php foreach ($noticias as $noticia): ?>
                    <article class="noticia-card">

                        <?php if ($noticia['imagem']): ?>
                            <img src="<?php echo sanitizar($noticia['imagem']); ?>" class="noticia-imagem">
                        <?php endif; ?>

                        <div class="noticia-conteudo">
                            <h3>
                                <a href="noticia.php?id=<?php echo $noticia['id']; ?>">
                                    <?php echo sanitizar($noticia['titulo']); ?>
                                </a>
                            </h3>

                            <p><?php echo gerar_resumo($noticia['noticia'], 250); ?></p>

                            <div class="noticia-meta">

                                <span class="autor">
                                    <?php if (!empty($noticia['foto_autor'])): ?>
                                        <img src="<?php echo $noticia['foto_autor']; ?>"
                                            style="width:25px; height:25px; border-radius:50%; object-fit:cover; vertical-align:middle;">
                                    <?php endif; ?>

                                    <?php echo sanitizar($noticia['nome_autor']); ?>
                                </span>

                                <span class="data">
                                    <?php echo formatar_data($noticia['data']); ?>
                                </span>
                            </div>

                            <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="btn-ler">
                                Ler →
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>

        </div>
    </main>

</body>

</html>