<?php
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

$usuario = obter_usuario($conexao, $_SESSION['usuario_id']);
$noticias = obter_noticias_autor($conexao, $_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Saúde e Bem-Estar</title>
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
            <div class="dashboard-secao">

                <div class="dashboard-card">
                    <h3>Meu Perfil</h3>
                    <div class="usuario-info">
                        <p><strong>Nome:</strong> <?php echo sanitizar($usuario['nome']); ?></p>
                        <p><strong>Email:</strong> <?php echo sanitizar($usuario['email']); ?></p>
                        <p><strong>Tipo de conta:</strong> <?php echo $usuario['adm'] ? '👑 Administrador' : '✍️ Autor'; ?></p>
                    </div>
                    <div class="dashboard-acoes">
                        <a href="nova_noticia.php" class="btn-primary">+ Nova Notícia</a>
                        <a href="editar_usuario.php" class="btn-secondary">Editar Perfil</a>
                        <?php if (usuario_adm()): ?>
                            <a href="admin.php" class="nav-link-adm">👑 Painel ADM</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h3>Minhas Notícias (<?php echo count($noticias); ?>)</h3>

                    <?php if (empty($noticias)): ?>
                        <div class="sem-noticias">
                            <p>Você ainda não publicou nenhuma notícia.</p>
                            <a href="nova_noticia.php" class="btn-primary">Publicar minha primeira notícia</a>
                        </div>
                    <?php else: ?>
                        <div class="noticias-tabela">
                            <div class="tabela-header">
                                <div>Título</div>
                                <div>Data</div>
                                <div>Ações</div>
                            </div>
                            <?php foreach ($noticias as $noticia): ?>
                                <div class="tabela-linha">
                                    <div class="col-titulo">
                                        <a href="noticia.php?id=<?php echo $noticia['id']; ?>">
                                            <?php echo sanitizar($noticia['titulo']); ?>
                                        </a>
                                    </div>
                                    <div><?php echo formatar_data($noticia['data']); ?></div>
                                    <div class="col-acoes">
                                        <a href="editar_noticia.php?id=<?php echo $noticia['id']; ?>" class="btn-primary btn-small">Editar</a>
                                        <a href="excluir_noticia.php?id=<?php echo $noticia['id']; ?>" class="btn-danger btn-small"
                                            onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">Excluir</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

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
