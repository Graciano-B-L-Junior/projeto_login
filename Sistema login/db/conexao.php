<?php
session_start();
$modo = "local";

if ($modo == "local") {
    $servidor = "localhost";
    $usuario = "root";
    $banco = "login";
    $senha = "";
} else if ($modo == "produção") {
    $servidor = "";
    $usuario = "";
    $banco = "";
    $senha = "";
}

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    #echo "banco conectado com sucesso";
} catch (PDOException $erro) {
    echo "falha ao se conectar ao banco " . $erro->getMessage();
}
function limparPost($dados)
{
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}
function auth($token)
{   
    global $pdo;
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE token= ? LIMIT 1");
    $sql->execute([$token]);
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        //SE NÃO ACHAR O USUARIO
        return false;
    } else {
        return $usuario;
    }
}
