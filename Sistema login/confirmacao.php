<?php
require('db/conexao.php');

if (isset($_GET['cod_confirmacao']) and !empty($_GET['cod_confirmacao'])) {
    //LIMPADO O GET
    $cod = limparPost($_GET['cod_confirmacao']);

    //VERIFICAR SE EXISTE ESTE USUARIO
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE codigo_confirmacao = ? LIMIT 1");
    $sql->execute([$cod]);
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    if ($usuario) {
        //ATUALIZA STATUS PARA CONFIRMADO
        $sql = $pdo->prepare("UPDATE usuarios SET status=? WHERE codigo_confirmacao=? ");
        $status = "confirmado";
        if ($sql->execute([$status, $cod])) {
            header('location: index.php?result=ok');
        }
    }
    else{
        echo "<h1> Código de confirmação inválido </h1>";
    }
}
