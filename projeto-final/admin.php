<?php
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_adm.php';

// Ações rápidas via GET
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $id   = (int)$_GET['id'];
    $acao = $_GET['acao'];
    $msg  = '';

    if ($acao === 'aprovar')          { $r = aprovar_usuario($conexao, $id);     $msg = $r['mensagem']; }
    elseif ($acao === 'revogar')      { $r = revogar_aprovacao($conexao, $id);   $msg = $r['mensagem']; }
    elseif ($acao === 'tornar_adm')   { $r = tornar_adm($conexao, $id);          $msg = "Usuário promovido a ADM"; }
    elseif ($acao === 'remover_adm')  { $r = remover_adm($conexao, $id);         $msg = "Privilégios de ADM removidos"; }
    elseif ($acao === 'desativar')    { $r = deletar_usuario($conexao, $id);      $msg = "Usuário desativado"; }
    elseif ($acao === 'ativar')       {
        $conexao->query("UPDATE usuarios SET ativo=1 WHERE id=$id");
        $msg = "Usuário ativado";
    }
    elseif ($acao === 'excluir') {
        $r   = excluir_usuario_permanente($conexao, $id);
        $msg = $r['sucesso'] ? "Usuário excluído permanentemente" : $r['mensagem'];
    }

    header("Location: admin.php?msg=" . urlencode($msg));
    exit();
}

$msg_flash = $_GET['msg'] ?? '';
$usuarios  = listar_todos_usuarios($conexao);

$total     = count($usuarios);
$pendentes = count(array_filter($usuarios, fn($u) => !$u['aprovado'] && !$u['adm'] && $u['ativo']));
$aprovados = count(array_filter($usuarios, fn($u) => ($u['aprovado'] || $u['adm']) && $u['ativo']));
$admins    = count(array_filter($usuarios, fn($u) => $u['adm']));

$usuario_atual = obter_usuario($conexao, $_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel ADM - Saúde e Bem-Estar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo"><h1>Saúde & Bem-Estar</h1></div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Ver Portal</a>
                <span class="user-info">
                    <?php if (!empty($usuario_atual['foto'])): ?>
                        <img src="<?php echo sanitizar($usuario_atual['foto']); ?>"
                            style="width:26px;height:26px;border-radius:50%;object-fit:cover;">
                    <?php endif; ?>
                    👑 <?php echo sanitizar($_SESSION['usuario_nome']); ?>
                </span>
                <a href="dashboard.php" class="nav-link">Dashboard</a>
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
                    <h2>Gerenciar Usuários</h2>
                    <p>Controle de acesso e permissões</p>
                </div>
                <a href="admin_criar_usuario.php" class="btn-novo-adm">+ Novo Usuário</a>
            </div>

            <?php if ($msg_flash): ?>
                <div class="flash-msg">✅ <?php echo sanitizar($msg_flash); ?></div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-num"><?php echo $total; ?></div>
                    <div class="stat-label">Total de Usuários</div>
                </div>
                <div class="stat-card pendente">
                    <div class="stat-num"><?php echo $pendentes; ?></div>
                    <div class="stat-label">Aguardando Aprovação</div>
                </div>
                <div class="stat-card">
                    <div class="stat-num"><?php echo $aprovados; ?></div>
                    <div class="stat-label">Com Acesso Liberado</div>
                </div>
                <div class="stat-card">
                    <div class="stat-num"><?php echo $admins; ?></div>
                    <div class="stat-label">Administradores</div>
                </div>
            </div>

            <div class="tab-filter">
                <button class="tab-btn ativo" onclick="filtrar('todos', this)">Todos (<?php echo $total; ?>)</button>
                <button class="tab-btn" onclick="filtrar('pendente', this)">⏳ Pendentes (<?php echo $pendentes; ?>)</button>
                <button class="tab-btn" onclick="filtrar('aprovado', this)">✅ Aprovados (<?php echo $aprovados; ?>)</button>
                <button class="tab-btn" onclick="filtrar('adm', this)">👑 ADMs (<?php echo $admins; ?>)</button>
            </div>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3>Lista de Usuários</h3>
                    <span class="adm-count"><?php echo $total; ?> usuários cadastrados</span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Email</th>
                                <th>Cadastro</th>
                                <th>Status</th>
                                <th>Tipo</th>
                                <th>Acesso</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($usuarios as $u):
                            $e_adm    = (bool)$u['adm'];
                            $ativo    = (bool)$u['ativo'];
                            $aprovado = (bool)$u['aprovado'];
                            $eu_mesmo = ($u['id'] == $_SESSION['usuario_id']);

                            $filtro_class = 'row-todos ';
                            if (!$ativo) $filtro_class .= 'row-inativo';
                            elseif ($e_adm) $filtro_class .= 'row-adm row-aprovado';
                            elseif ($aprovado) $filtro_class .= 'row-aprovado';
                            else $filtro_class .= 'row-pendente';
                        ?>
                        <tr class="<?php echo $filtro_class; ?>">
                            <td>
                                <div class="usuario-nome-wrap">
                                    <?php if (!empty($u['foto'])): ?>
                                        <img src="<?php echo sanitizar($u['foto']); ?>" class="usuario-avatar">
                                    <?php else: ?>
                                        <span class="avatar-placeholder"><?php echo mb_substr($u['nome'], 0, 1); ?></span>
                                    <?php endif; ?>
                                    <span>
                                        <?php echo sanitizar($u['nome']); ?>
                                        <?php if ($eu_mesmo): ?><span class="badge badge-azul">você</span><?php endif; ?>
                                    </span>
                                </div>
                            </td>
                            <td style="color:var(--cor-texto-muted);"><?php echo sanitizar($u['email']); ?></td>
                            <td style="color:var(--cor-texto-muted);white-space:nowrap;"><?php echo formatar_data($u['data_criacao']); ?></td>
                            <td>
                                <?php if (!$ativo): ?>
                                    <span class="badge badge-cinza">Inativo</span>
                                <?php else: ?>
                                    <span class="badge badge-verde">Ativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($e_adm): ?>
                                    <span class="badge badge-roxo">👑 ADM</span>
                                <?php else: ?>
                                    <span class="badge badge-cinza">Usuário</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($e_adm || $aprovado): ?>
                                    <span class="badge badge-verde">✅ Liberado</span>
                                <?php else: ?>
                                    <span class="badge badge-ambar">⏳ Pendente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="acoes-wrap">
                                    <?php if (!$e_adm): ?>
                                        <?php if (!$aprovado): ?>
                                            <a href="admin.php?acao=aprovar&id=<?php echo $u['id']; ?>" class="btn-acao btn-aprovar" title="Aprovar acesso">✅ Aprovar</a>
                                        <?php else: ?>
                                            <a href="admin.php?acao=revogar&id=<?php echo $u['id']; ?>" class="btn-acao btn-revogar"
                                                onclick="return confirm('Revogar acesso de <?php echo sanitizar($u['nome']); ?>?')">🚫 Revogar</a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (!$eu_mesmo): ?>
                                        <?php if (!$e_adm): ?>
                                            <a href="admin.php?acao=tornar_adm&id=<?php echo $u['id']; ?>" class="btn-acao btn-adm"
                                                onclick="return confirm('Promover <?php echo sanitizar($u['nome']); ?> a ADM?')">👑 ADM</a>
                                        <?php else: ?>
                                            <a href="admin.php?acao=remover_adm&id=<?php echo $u['id']; ?>" class="btn-acao btn-revogar"
                                                onclick="return confirm('Remover privilégios de ADM?')">⬇️ Rebaixar</a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <a href="admin_editar_usuario.php?id=<?php echo $u['id']; ?>" class="btn-acao btn-editar">✏️ Editar</a>

                                    <?php if (!$eu_mesmo): ?>
                                        <?php if ($ativo): ?>
                                            <a href="admin.php?acao=desativar&id=<?php echo $u['id']; ?>" class="btn-acao btn-ativar"
                                                onclick="return confirm('Desativar este usuário?')">🔒 Desativar</a>
                                        <?php else: ?>
                                            <a href="admin.php?acao=ativar&id=<?php echo $u['id']; ?>" class="btn-acao btn-aprovar">🔓 Ativar</a>
                                        <?php endif; ?>
                                        <a href="admin.php?acao=excluir&id=<?php echo $u['id']; ?>" class="btn-acao btn-excluir"
                                            onclick="return confirm('ATENÇÃO: Excluir permanentemente <?php echo sanitizar($u['nome']); ?>? Isso apagará todas as notícias deste usuário. Esta ação não pode ser desfeita!')">🗑️</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
    function filtrar(tipo, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('ativo'));
        btn.classList.add('ativo');
        const rows = document.querySelectorAll('.adm-table tbody tr');
        rows.forEach(row => {
            row.style.display = (tipo === 'todos' || row.classList.contains('row-' + tipo)) ? '' : 'none';
        });
    }
    </script>
</body>
</html>
