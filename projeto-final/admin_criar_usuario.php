<?php
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_adm.php';

$erro   = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = $_POST['nome']     ?? '';
    $email    = $_POST['email']    ?? '';
    $senha    = $_POST['senha']    ?? '';
    $conf     = $_POST['confirmar_senha'] ?? '';
    $adm      = isset($_POST['adm'])      ? 1 : 0;
    $aprovado = isset($_POST['aprovado']) ? 1 : 0;

    $erros = validar_formulario(['nome' => $nome, 'email' => $email, 'senha' => $senha]);
    if ($senha !== $conf) $erros[] = "As senhas não coincidem";

    if (empty($erros)) {
        if ($adm) $aprovado = 1;
        $resultado = criar_usuario($conexao, $nome, $email, $senha, $adm, $aprovado);
        if ($resultado['sucesso']) {
            $sucesso = "Usuário criado com sucesso!";
            $_POST = [];
        } else {
            $erro = $resultado['mensagem'];
        }
    } else {
        $erro = implode('<br>', $erros);
    }
}

$usuario_atual = obter_usuario($conexao, $_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Usuário - ADM</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo"><h1>Saúde & Bem-Estar</h1></div>
            <nav class="nav">
                <span class="user-info">👑 <?php echo sanitizar($_SESSION['usuario_nome']); ?></span>
                <a href="admin.php" class="nav-link">← Voltar ao Painel</a>
                <a href="index.php" class="nav-link">Ver Site</a>
                <a href="logout.php" class="btn-danger btn-small">Logout</a>
            </nav>
        </div>
    </header>

    <div class="adm-layout">
        <aside class="adm-sidebar">
            <div class="adm-sidebar-title">Administração</div>
            <a href="admin.php" class="adm-nav-link">👥 Usuários</a>
            <a href="admin_criar_usuario.php" class="adm-nav-link ativo">➕ Novo Usuário</a>
            <div class="adm-sidebar-title" style="margin-top:20px;">Portal</div>
            <a href="nova_noticia.php" class="adm-nav-link">📝 Nova Notícia</a>
            <a href="dashboard.php" class="adm-nav-link">📊 Dashboard</a>
            <a href="index.php" class="adm-nav-link">🏠 Ver Site</a>
        </aside>

        <main class="adm-main">
            <div class="adm-page-header">
                <div>
                    <h2>Criar Novo Usuário</h2>
                    <p>Crie usuários diretamente com permissões configuradas</p>
                </div>
            </div>

            <div class="adm-form-card">
                <div class="adm-form-header">
                    <h3>Dados do Usuário</h3>
                </div>
                <div class="adm-form-body">
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo $erro; ?></div>
                    <?php endif; ?>
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success">✅ <?php echo $sucesso; ?> <a href="admin.php">Ver todos os usuários</a></div>
                    <?php endif; ?>

                    <form method="POST" class="form">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nome Completo *</label>
                                <input type="text" name="nome" required value="<?php echo sanitizar($_POST['nome'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" required value="<?php echo sanitizar($_POST['email'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Senha * <span style="font-size:0.78rem;color:var(--cor-texto-muted);">(mín. 6 caracteres)</span></label>
                                <input type="password" name="senha" required>
                            </div>
                            <div class="form-group">
                                <label>Confirmar Senha *</label>
                                <input type="password" name="confirmar_senha" required>
                            </div>
                        </div>

                        <div class="permissoes-titulo">Permissões</div>

                        <div class="toggle-wrap">
                            <div>
                                <div class="toggle-label">✅ Aprovado para login</div>
                                <div class="toggle-desc">Usuário pode fazer login imediatamente</div>
                            </div>
                            <input type="checkbox" name="aprovado" <?php echo isset($_POST['aprovado']) ? 'checked' : 'checked'; ?>>
                        </div>
                        <div class="toggle-wrap">
                            <div>
                                <div class="toggle-label">👑 Administrador</div>
                                <div class="toggle-desc">Acesso total ao painel de administração</div>
                            </div>
                            <input type="checkbox" name="adm" <?php echo isset($_POST['adm']) ? 'checked' : ''; ?>>
                        </div>

                        <div class="adm-form-acoes">
                            <button type="submit" class="btn-primary">Criar Usuário</button>
                            <a href="admin.php" class="adm-cancelar-btn">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
