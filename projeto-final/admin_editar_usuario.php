<?php
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_adm.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: admin.php"); exit(); }

$usuario = obter_usuario($conexao, $id);
if (!$usuario) { header("Location: admin.php?msg=" . urlencode("Usuário não encontrado")); exit(); }

$erro   = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = $_POST['nome']      ?? '';
    $email     = $_POST['email']     ?? '';
    $adm       = isset($_POST['adm'])      ? 1 : 0;
    $aprovado  = isset($_POST['aprovado']) ? 1 : 0;
    $ativo     = isset($_POST['ativo'])    ? 1 : 0;
    $nova_senha = trim($_POST['nova_senha'] ?? '');

    $erros = [];
    if (empty(trim($nome)))  $erros[] = "Nome é obrigatório";
    if (empty(trim($email))) $erros[] = "Email é obrigatório";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Email inválido";
    if ($nova_senha && strlen($nova_senha) < 6) $erros[] = "Nova senha deve ter mínimo 6 caracteres";

    if ($id == $_SESSION['usuario_id'] && !$adm) {
        $erros[] = "Você não pode remover seus próprios privilégios de ADM";
        $adm = 1;
    }

    if ($adm) $aprovado = 1;

    if (empty($erros)) {
        $resultado = atualizar_usuario_adm(
            $conexao, $id, $nome, $email, $adm, $aprovado, $ativo,
            $nova_senha ?: null
        );
        if ($resultado['sucesso']) {
            $sucesso  = $resultado['mensagem'];
            $usuario  = obter_usuario($conexao, $id);
        } else {
            $erro = $resultado['mensagem'];
        }
    } else {
        $erro = implode('<br>', $erros);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - ADM</title>
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
            <a href="admin.php" class="adm-nav-link ativo">👥 Usuários</a>
            <a href="admin_criar_usuario.php" class="adm-nav-link">➕ Novo Usuário</a>
            <div class="adm-sidebar-title" style="margin-top:20px;">Portal</div>
            <a href="nova_noticia.php" class="adm-nav-link">📝 Nova Notícia</a>
            <a href="dashboard.php" class="adm-nav-link">📊 Dashboard</a>
            <a href="index.php" class="adm-nav-link">🏠 Ver Site</a>
        </aside>

        <main class="adm-main">
            <div class="adm-page-header">
                <div>
                    <h2>Editar Usuário</h2>
                    <p>Altere os dados, senha e permissões do usuário</p>
                </div>
            </div>

            <div class="adm-form-card">
                <div class="adm-form-header">
                    <?php if (!empty($usuario['foto'])): ?>
                        <img src="<?php echo sanitizar($usuario['foto']); ?>" class="avatar-lg">
                    <?php else: ?>
                        <div class="avatar-placeholder-lg"><?php echo mb_substr($usuario['nome'], 0, 1); ?></div>
                    <?php endif; ?>
                    <div>
                        <div style="font-weight:600;"><?php echo sanitizar($usuario['nome']); ?></div>
                        <div style="font-size:0.82rem;color:var(--cor-texto-muted);">
                            ID #<?php echo $usuario['id']; ?> · Cadastro em <?php echo formatar_data($usuario['data_criacao']); ?>
                        </div>
                    </div>
                </div>

                <div class="adm-form-body">
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo $erro; ?></div>
                    <?php endif; ?>
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success">✅ <?php echo sanitizar($sucesso); ?></div>
                    <?php endif; ?>

                    <form method="POST" class="form">

                        <div class="section-divider">Dados Pessoais</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nome Completo *</label>
                                <input type="text" name="nome" required value="<?php echo sanitizar($usuario['nome']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" required value="<?php echo sanitizar($usuario['email']); ?>">
                            </div>
                        </div>

                        <div class="section-divider">Redefinir Senha</div>
                        <div class="form-group">
                            <label>Nova Senha <span style="font-size:0.78rem;color:var(--cor-texto-muted);">(deixe em branco para manter a atual)</span></label>
                            <input type="password" name="nova_senha" placeholder="Nova senha (mín. 6 caracteres)">
                        </div>

                        <div class="section-divider">Permissões & Status</div>

                        <div class="toggle-wrap">
                            <div>
                                <div class="toggle-label">✅ Aprovado para login</div>
                                <div class="toggle-desc">Usuário pode fazer login no portal</div>
                            </div>
                            <input type="checkbox" name="aprovado" <?php echo $usuario['aprovado'] ? 'checked' : ''; ?>>
                        </div>

                        <div class="toggle-wrap">
                            <div>
                                <div class="toggle-label">👑 Administrador</div>
                                <div class="toggle-desc">Acesso total ao painel de administração</div>
                            </div>
                            <input type="checkbox" name="adm"
                                <?php echo $usuario['adm'] ? 'checked' : ''; ?>
                                <?php echo ($id == $_SESSION['usuario_id']) ? 'disabled' : ''; ?>>
                            <?php if ($id == $_SESSION['usuario_id']): ?>
                                <input type="hidden" name="adm" value="1">
                            <?php endif; ?>
                        </div>

                        <div class="toggle-wrap">
                            <div>
                                <div class="toggle-label">🔓 Conta Ativa</div>
                                <div class="toggle-desc">Desative para bloquear o usuário sem excluir</div>
                            </div>
                            <input type="checkbox" name="ativo"
                                <?php echo $usuario['ativo'] ? 'checked' : ''; ?>
                                <?php echo ($id == $_SESSION['usuario_id']) ? 'disabled' : ''; ?>>
                            <?php if ($id == $_SESSION['usuario_id']): ?>
                                <input type="hidden" name="ativo" value="1">
                            <?php endif; ?>
                        </div>

                        <div class="adm-form-acoes">
                            <button type="submit" class="btn-primary">Salvar Alterações</button>
                            <a href="admin.php" class="adm-cancelar-btn">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
