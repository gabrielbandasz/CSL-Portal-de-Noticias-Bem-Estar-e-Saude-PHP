<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'funcoes.php';

if (!usuario_logado()) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
if (!usuario_adm()) {
    header("Location: dashboard.php?erro=acesso_negado");
    exit();
}
?>
