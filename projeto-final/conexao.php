<?php
// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portal_saude_bem_estar');

// Criando conexão
$conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// Criar banco de dados se não existir (conexão sem DB)
$conexao_temp = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conexao_temp->connect_error) {
    die("Erro na conexão temporária: " . $conexao_temp->connect_error);
}

$sql_criar_db = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conexao_temp->query($sql_criar_db) === TRUE) {
    $conexao_temp->close();
    $conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conexao->connect_error) {
        die("Erro na reconexão: " . $conexao->connect_error);
    }
} else {
    die("Erro ao criar banco de dados: " . $conexao_temp->error);
}

$conexao->set_charset("utf8mb4");

// Criar tabelas se não existirem
$sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1,
    adm TINYINT(1) NOT NULL DEFAULT 0,
    aprovado TINYINT(1) NOT NULL DEFAULT 0,
    foto VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$sql_noticias = "CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    noticia LONGTEXT NOT NULL,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    autor INT NOT NULL,
    imagem VARCHAR(255),
    FOREIGN KEY (autor) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conexao->query($sql_usuarios)) {
    echo "Aviso: Erro ao criar tabela usuarios: " . $conexao->error . "<br>";
}

if (!$conexao->query($sql_noticias)) {
    echo "Aviso: Erro ao criar tabela noticias: " . $conexao->error . "<br>";
}

// Adicionar colunas adm e aprovado se não existirem (para bancos já criados)
$cols = $conexao->query("SHOW COLUMNS FROM usuarios LIKE 'adm'");
if ($cols && $cols->num_rows === 0) {
    $conexao->query("ALTER TABLE usuarios ADD COLUMN adm TINYINT(1) NOT NULL DEFAULT 0 AFTER ativo");
}
$cols2 = $conexao->query("SHOW COLUMNS FROM usuarios LIKE 'aprovado'");
if ($cols2 && $cols2->num_rows === 0) {
    $conexao->query("ALTER TABLE usuarios ADD COLUMN aprovado TINYINT(1) NOT NULL DEFAULT 0 AFTER adm");
}
?>
