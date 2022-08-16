<?php 
    require('db/conexao.php');
    //VERIFICAR SE TEM AUTORIZAÇÃO
    $usuario = auth($_SESSION['token']);
    if($usuario){
        echo "<h1>Seja bem vindo". $usuario['nome']." </h1>";
        echo "<br><br><a href='logout.php'>Sair do sistema</a>";
    }else{
        header('location: index.php');
    }
?>