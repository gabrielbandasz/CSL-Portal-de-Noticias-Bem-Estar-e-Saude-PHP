<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$noticia_por_pagina = 5;

$termo_busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

if (!empty($termo_busca)) {
    $noticias = buscar_noticias($conexao, $termo_busca, $noticia_por_pagina, $pagina_atual);
    $total_noticias = contar_busca($conexao, $termo_busca);
} else {
    $noticias = listar_noticias($conexao, $noticia_por_pagina, $pagina_atual);
    $total_noticias = contar_noticias($conexao);
}

$usuario = null;
if (usuario_logado()) {
    $usuario = obter_usuario($conexao, $_SESSION['usuario_id']);
}

$total_paginas = ceil($total_noticias / $noticia_por_pagina);
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

                <form method="GET" action="index.php" class="busca-form">
                    <div class="busca-container">
                        <span class="icone-busca">🔍</span>
                        <input
                            type="text"
                            name="busca"
                            class="busca-input"
                            placeholder="Buscar notícias..."
                            value="<?php echo isset($_GET['busca']) ? sanitizar($_GET['busca']) : ''; ?>">
                        <?php if (!empty($_GET['busca'])): ?>
                            <a href="index.php" class="busca-limpar">✕</a>
                        <?php endif; ?>
                    </div>
                </form>

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

            <?php if (!empty($termo_busca)): ?>
                <section class="hero" style="padding:40px 48px;margin-bottom:40px;">
                    <h2 style="font-size:36px;">Resultados para "<?php echo sanitizar($termo_busca); ?>"</h2>
                    <p><?php echo $total_noticias; ?> notícia(s) encontrada(s)</p>
                </section>
            <?php else: ?>
                <section class="hero">
                    <h2>Últimas Notícias sobre Saúde e Bem-Estar</h2>
                    <p>Fique informado com as melhores notícias sobre saúde, nutrição, fitness e qualidade de vida.</p>
                </section>
            <?php endif; ?>

            <section class="noticias-lista">
                <?php if (empty($noticias)): ?>
                    <div class="sem-noticias">
                        <p>Nenhuma notícia encontrada.</p>
                        <?php if (!empty($termo_busca)): ?>
                            <a href="index.php" class="btn-secondary">Ver todas as notícias</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($noticias as $noticia): ?>
                        <article class="noticia-card">

                            <?php if ($noticia['imagem']): ?>
                                <img src="<?php echo sanitizar($noticia['imagem']); ?>" class="noticia-imagem" alt="<?php echo sanitizar($noticia['titulo']); ?>">
                            <?php else: ?>
                                <div class="noticia-imagem-placeholder">📰</div>
                            <?php endif; ?>

                            <div class="noticia-conteudo">
                                <h3>
                                    <a href="noticia.php?id=<?php echo $noticia['id']; ?>">
                                        <?php echo sanitizar($noticia['titulo']); ?>
                                    </a>
                                </h3>

                                <p class="noticia-resumo"><?php echo gerar_resumo($noticia['noticia'], 250); ?></p>

                                <div class="noticia-meta">
                                    <span class="autor">
                                        <?php if (!empty($noticia['foto_autor'])): ?>
                                            <img src="<?php echo sanitizar($noticia['foto_autor']); ?>"
                                                style="width:22px; height:22px; border-radius:50%; object-fit:cover;">
                                        <?php endif; ?>
                                        <?php echo sanitizar($noticia['nome_autor']); ?>
                                    </span>
                                    <span class="data">
                                        <?php echo formatar_data($noticia['data']); ?>
                                    </span>
                                </div>

                                <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="btn-ler">Ler</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <?php if ($total_paginas > 1): ?>
                <nav class="paginacao">
                    <?php if ($pagina_atual > 1): ?>
                        <a href="?page=<?php echo $pagina_atual - 1; ?><?php echo !empty($termo_busca) ? '&busca=' . urlencode($termo_busca) : ''; ?>" class="btn-paginacao">← Anterior</a>
                    <?php endif; ?>
                    <span class="paginacao-info">Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?></span>
                    <?php if ($pagina_atual < $total_paginas): ?>
                        <a href="?page=<?php echo $pagina_atual + 1; ?><?php echo !empty($termo_busca) ? '&busca=' . urlencode($termo_busca) : ''; ?>" class="btn-paginacao">Próxima →</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>

        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Portal de Notícias - Saúde e Bem-Estar. Todos os direitos reservados.</p>
        </div>
    </footer>

</body>
</html>
