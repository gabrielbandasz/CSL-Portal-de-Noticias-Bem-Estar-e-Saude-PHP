<?php
// Inicia a sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carrega as funções do sistema
require_once 'funcoes.php';

// Verifica se o usuário está logado
if (!usuario_logado()) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>