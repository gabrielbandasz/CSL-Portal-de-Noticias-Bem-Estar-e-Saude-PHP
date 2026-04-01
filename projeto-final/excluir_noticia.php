<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: dashboard.php");
    exit();
}

$noticia = obter_noticia($conexao, $id);

if (!$noticia || $noticia['autor'] != $_SESSION['usuario_id']) {
    header("Location: dashboard.php");
    exit();
}

// Deletar imagem
if (!empty($noticia['imagem']) && file_exists($noticia['imagem'])) {
    unlink($noticia['imagem']);
}

// Deletar no banco
$resultado = deletar_noticia($conexao, $id);

if ($resultado['sucesso']) {
    header("Location: dashboard.php?sucesso=Deletado");
} else {
    header("Location: dashboard.php?erro=Erro ao deletar");
}
exit();