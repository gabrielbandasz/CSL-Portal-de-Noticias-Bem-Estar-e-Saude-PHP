<?php
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

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['noticia'] ?? '');
    $imagem = $noticia['imagem'];

    $erros = validar_formulario([
        'titulo' => $titulo,
        'noticia' => $conteudo
    ]);

    // upload imagem
    if (!empty($_FILES['imagem']['name'])) {

        $upload = fazer_upload_imagem($_FILES['imagem']);

        if ($upload['sucesso']) {

            if (!empty($imagem) && file_exists($imagem)) {
                unlink($imagem);
            }

            $imagem = $upload['caminho'];

        } else {
            $erros[] = $upload['mensagem'];
        }
    }

    if (empty($erros)) {

        $resultado = atualizar_noticia($conexao, $id, $titulo, $conteudo, $imagem);

        if ($resultado['sucesso']) {
            $sucesso = "Atualizado com sucesso!";
            header("Refresh:2; url=noticia.php?id=$id");
        } else {
            $erro = $resultado['mensagem'];
        }

    } else {
        $erro = implode("<br>", $erros);
    }
}
?>