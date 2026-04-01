<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Evita redeclaração
if (defined('FUNCOES_CARREGADAS')) return;
define('FUNCOES_CARREGADAS', true);

// Inicia sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ===================== VALIDAÇÃO ===================== */
function validar_formulario($dados)
{
    $erros = [];

    if (isset($dados['nome']) && empty(trim($dados['nome']))) {
        $erros[] = "Nome é obrigatório";
    }

    if (isset($dados['email'])) {
        if (empty(trim($dados['email']))) {
            $erros[] = "Email é obrigatório";
        } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Email inválido";
        }
    }

    if (isset($dados['senha'])) {
        if (empty(trim($dados['senha']))) {
            $erros[] = "Senha é obrigatória";
        } elseif (strlen($dados['senha']) < 6) {
            $erros[] = "Senha deve ter no mínimo 6 caracteres";
        }
    }

    if (isset($dados['titulo']) && empty(trim($dados['titulo']))) {
        $erros[] = "Título é obrigatório";
    }

    if (isset($dados['noticia']) && empty(trim($dados['noticia']))) {
        $erros[] = "Conteúdo é obrigatório";
    }

    return $erros;
}

/* ===================== AUTENTICAÇÃO ===================== */
function criptografar_senha($senha)
{
    return password_hash($senha, PASSWORD_BCRYPT);
}

function verificar_senha($senha, $hash)
{
    return password_verify($senha, $hash);
}

function usuario_logado()
{
    return isset($_SESSION['usuario_id']);
}

function fazer_login($conexao, $email, $senha)
{
    $email = $conexao->real_escape_string($email);

    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND ativo = TRUE";
    $res = $conexao->query($sql);

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (verificar_senha($senha, $user['senha'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['nome'];
            $_SESSION['usuario_email'] = $user['email'];
            return true;
        }
    }
    return false;
}

function fazer_logout()
{
    session_destroy();
    header("Location: index.php");
    exit();
}

/* ===================== USUÁRIOS ===================== */
function obter_usuario($conexao, $id)
{
    $id = (int)$id;
    $res = $conexao->query("SELECT * FROM usuarios WHERE id = $id");
    return $res->fetch_assoc();
}

function criar_usuario($conexao, $nome, $email, $senha)
{
    $nome = $conexao->real_escape_string($nome);
    $email = $conexao->real_escape_string($email);
    $senha = criptografar_senha($senha);

    $check = $conexao->query("SELECT id FROM usuarios WHERE email = '$email'");
    if ($check->num_rows > 0) {
        return ["sucesso" => false, "mensagem" => "Email já cadastrado"];
    }

    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha')";
    return $conexao->query($sql)
        ? ["sucesso" => true, "mensagem" => "Usuário criado"]
        : ["sucesso" => false, "mensagem" => "Erro ao criar"];
}

function atualizar_usuario($conexao, $id, $nome, $email, $foto = null)
{
    $id = (int)$id;
    $nome = $conexao->real_escape_string($nome);
    $email = $conexao->real_escape_string($email);

    $check = $conexao->query("SELECT id FROM usuarios WHERE email = '$email' AND id != $id");
    if ($check->num_rows > 0) {
        return ["sucesso" => false, "mensagem" => "Email já cadastrado"];
    }

    $sql = "UPDATE usuarios SET nome='$nome', email='$email'";

    if ($foto) {
        $foto = $conexao->real_escape_string($foto);
        $sql .= ", foto='$foto'";
    }

    $sql .= " WHERE id=$id";

    if ($conexao->query($sql)) {
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_email'] = $email;
        return ["sucesso" => true, "mensagem" => "Atualizado com sucesso"];
    }

    return ["sucesso" => false, "mensagem" => "Erro ao atualizar"];
}

function deletar_usuario($conexao, $id)
{
    $id = (int)$id;
    return $conexao->query("UPDATE usuarios SET ativo=FALSE WHERE id=$id")
        ? ["sucesso" => true, "mensagem" => "Conta deletada"]
        : ["sucesso" => false, "mensagem" => "Erro"];
}

/* ===================== BUSCA ===================== */
function buscar_noticias($conexao, $termo, $limite = 10, $pagina = 1)
{
    $termo = $conexao->real_escape_string($termo);
    $offset = ($pagina - 1) * $limite;

    $sql = "SELECT n.*, u.nome AS nome_autor, u.foto AS foto_autor
            FROM noticias n
            JOIN usuarios u ON n.autor = u.id
            WHERE n.titulo LIKE '%$termo%' 
               OR n.noticia LIKE '%$termo%'
               OR u.nome LIKE '%$termo%'
            ORDER BY n.data DESC
            LIMIT $limite OFFSET $offset";

    $res = $conexao->query($sql);

    if (!$res) {
        die("Erro na busca: " . $conexao->error);
    }

    $dados = [];

    while ($row = $res->fetch_assoc()) {
        $dados[] = $row;
    }

    return $dados;
}

function contar_busca($conexao, $termo)
{
    $termo = $conexao->real_escape_string($termo);

    $sql = "SELECT COUNT(*) total 
            FROM noticias n
            JOIN usuarios u ON n.autor = u.id
            WHERE n.titulo LIKE '%$termo%' 
               OR n.noticia LIKE '%$termo%' 
               OR u.nome LIKE '%$termo%'";

    $res = $conexao->query($sql);

    if (!$res) {
        die("Erro ao contar busca: " . $conexao->error);
    }

    return $res->fetch_assoc()['total'];
}

/* ===================== NOTÍCIAS ===================== */
function criar_noticia($conexao, $titulo, $conteudo, $autor, $imagem = null)
{
    $titulo = $conexao->real_escape_string($titulo);
    $conteudo = $conexao->real_escape_string($conteudo);
    $autor = (int)$autor;

    $img = $imagem ? "'" . $conexao->real_escape_string($imagem) . "'" : "NULL";

    $sql = "INSERT INTO noticias (titulo, noticia, autor, imagem)
            VALUES ('$titulo','$conteudo',$autor,$img)";

    return $conexao->query($sql)
        ? ["sucesso" => true]
        : ["sucesso" => false];
}

function listar_noticias($conexao, $limite = 10, $pagina = 1)
{
    $offset = ($pagina - 1) * $limite;

    $sql = "SELECT n.*, u.nome AS nome_autor, u.foto AS foto_autor
            FROM noticias n
            JOIN usuarios u ON n.autor = u.id
            ORDER BY n.data DESC
            LIMIT $limite OFFSET $offset";

    $res = $conexao->query($sql);
    $dados = [];

    while ($row = $res->fetch_assoc()) {
        $dados[] = $row;
    }

    return $dados;
}

function contar_noticias($conexao)
{
    $res = $conexao->query("SELECT COUNT(*) total FROM noticias");
    return $res->fetch_assoc()['total'];
}

function obter_noticias_autor($conexao, $id)
{
    $id = (int)$id;

    $sql = "SELECT n.*, u.nome as nome_autor, u.foto as foto_autor 
            FROM noticias n 
            JOIN usuarios u ON n.autor = u.id 
            WHERE n.autor = $id";

    $res = $conexao->query($sql);
    $dados = [];

    while ($row = $res->fetch_assoc()) {
        $dados[] = $row;
    }

    return $dados;
}

function obter_noticia($conexao, $id)
{
    $id = (int)$id;

    $sql = "SELECT n.*, u.nome as nome_autor, u.foto as foto_autor 
            FROM noticias n 
            JOIN usuarios u ON n.autor = u.id 
            WHERE n.id = $id";

    $resultado = $conexao->query($sql);

    if (!$resultado) {
        die("Erro ao buscar notícia: " . $conexao->error);
    }

    return $resultado->fetch_assoc();
}

function atualizar_noticia($conexao, $id, $titulo, $conteudo, $imagem = null)
{
    $id = (int)$id;
    $titulo = $conexao->real_escape_string($titulo);
    $conteudo = $conexao->real_escape_string($conteudo);

    $sql = "UPDATE noticias 
            SET titulo = '$titulo', noticia = '$conteudo'";

    // Atualiza imagem se tiver
    if ($imagem !== null) {
        $imagem = $conexao->real_escape_string($imagem);
        $sql .= ", imagem = '$imagem'";
    }

    $sql .= " WHERE id = $id";

    if ($conexao->query($sql)) {
        return ["sucesso" => true, "mensagem" => "Notícia atualizada"];
    }

    return ["sucesso" => false, "mensagem" => "Erro ao atualizar"];
}

/* ===================== UTIL ===================== */
function formatar_data($data)
{
    return date("d/m/Y H:i", strtotime($data));
}

function gerar_resumo($texto, $limite = 200)
{
    $texto = strip_tags($texto);
    return strlen($texto) > $limite ? substr($texto, 0, $limite) . "..." : $texto;
}

function sanitizar($texto)
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

/* ===================== UPLOAD ===================== */
function fazer_upload_imagem($arquivo)
{
    if (!isset($arquivo['name']) || empty($arquivo['name'])) {
        return ["sucesso" => false, "mensagem" => "Nenhuma imagem enviada"];
    }

    $ext = ['jpg', 'jpeg', 'png', 'gif'];
    $max = 5 * 1024 * 1024;

    $info = pathinfo($arquivo['name']);
    $extensao = strtolower($info['extension'] ?? '');

    if (!in_array($extensao, $ext)) {
        return ["sucesso" => false, "mensagem" => "Formato inválido"];
    }

    if ($arquivo['size'] > $max) {
        return ["sucesso" => false, "mensagem" => "Arquivo grande demais"];
    }

    $nome = uniqid() . "." . $extensao;
    $dir = "imagens/";

    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $caminho = $dir . $nome;

    if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
        return ["sucesso" => true, "caminho" => $caminho];
    }

    return ["sucesso" => false, "mensagem" => "Erro no upload"];
}

function deletar_noticia($conexao, $id)
{
    $id = (int)$id;

    $sql = "DELETE FROM noticias WHERE id = $id";

    if ($conexao->query($sql)) {
        return ["sucesso" => true, "mensagem" => "Notícia deletada"];
    }

    return [
        "sucesso" => false,
        "mensagem" => "Erro ao deletar: " . $conexao->error
    ];
}