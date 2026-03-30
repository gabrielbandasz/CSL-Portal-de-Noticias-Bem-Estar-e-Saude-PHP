<?php
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: dashboard.php");
    exit();
}

$noticia = obter_noticia($conexao, $id);

// Verificar se o usuário é o autor da notícia
if (!$noticia || $noticia['autor'] != $_SESSION['usuario_id']) {
    header("Location: dashboard.php");
    exit();
}

// Deletar notícia
if ($noticia['imagem'] && file_exists($noticia['imagem'])) {
    unlink($noticia['imagem']);
}

$resultado = deletar_noticia($conexao, $id);

if ($resultado['sucesso']) {
    header("Location: dashboard.php?mensagem=" . urlencode($resultado['mensagem']));
} else {
    header("Location: noticia.php?id=$id&erro=" . urlencode($resultado['mensagem']));
}
exit();
?>