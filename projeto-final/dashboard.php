<?php
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

$usuario_id = $_SESSION['usuario_id'];
$usuario = obter_usuario($conexao, $usuario_id);
$noticias = obter_noticias_autor($conexao, $usuario_id);
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
                <span class="user-info">
                    <?php if (!empty($usuario['foto'])): ?>
                        <img src="<?php echo $usuario['foto']; ?>"
                            style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                    <?php endif; ?>
                    Olá, <?php echo sanitizar($_SESSION['usuario_nome']); ?>
                </span> <a href="nova_noticia.php" class="nav-link btn-primary">+ Nova Notícia</a>
                <a href="logout.php" class="nav-link btn-danger">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <section class="dashboard-secao">
                <h2>Dashboard</h2>

                <!-- Informações do Usuário -->
                <div class="dashboard-card">
                    <h3>📋 Suas Informações</h3>
                    <div class="usuario-info">
                        <p><strong>Nome:</strong> <?php echo sanitizar($usuario['nome']); ?></p>
                        <p><strong>Email:</strong> <?php echo sanitizar($usuario['email']); ?></p>
                        <p><strong>Membro desde:</strong> <?php echo formatar_data($usuario['data_criacao']); ?></p>
                    </div>
                    <div class="dashboard-acoes">
                        <a href="editar_usuario.php" class="btn-primary">Editar Perfil</a>
                        <a href="excluir_usuario.php" class="btn-danger" onclick="return confirm('Tem certeza? Esta ação não pode ser desfeita.');"><u>Deletar Conta</u></a>
                    </div>
                </div>

                <!-- Suas Notícias -->
                <div class="dashboard-card">
                    <h3>📰 Suas Notícias (<?php echo count($noticias); ?>)</h3>

                    <?php if (count($noticias) > 0): ?>
                        <div class="noticias-tabela">
                            <div class="tabela-header">
                                <div class="col-titulo">Título</div>
                                <div class="col-data">Data</div>
                                <div class="col-acoes">Ações</div>
                            </div>
                            <?php foreach ($noticias as $noticia): ?>
                                <div class="tabela-linha">
                                    <div class="col-titulo">
                                        <a href="noticia.php?id=<?php echo $noticia['id']; ?>">
                                            <?php echo sanitizar(substr($noticia['titulo'], 0, 50)); ?>
                                        </a>
                                    </div>
                                    <div class="col-data">
                                        <?php echo formatar_data($noticia['data']); ?>
                                    </div>
                                    <div class="col-acoes">
                                        <a href="editar_noticia.php?id=<?php echo $noticia['id']; ?>" class="btn-small">Editar</a>
                                        <a href="excluir_noticia.php?id=<?php echo $noticia['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Tem certeza?');">Deletar</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="sem-noticias-texto">Você ainda não publicou nenhuma notícia.</p>
                        <a href="nova_noticia.php" class="btn-primary">Publicar sua primeira notícia</a>
                    <?php endif; ?>
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