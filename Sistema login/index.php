<?php 
require('db/conexao.php');

if(isset($_POST['email']) and isset($_POST['senha']) and !empty($_POST['email']) and !empty($_POST['senha'])){
    //RECEBER OS DADOS VINDO DO POST E LIMPAR
    $email = limparPost($_POST['email']);
    $senha = limparPost($_POST['senha']);
    $senha_cript = sha1($senha);

    //VERIFICAR SE EXISTE ESTE USUARIO
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = ? LIMIT 1");
    $sql->execute([$email,$senha_cript]);
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    if($usuario){
        //USUARIO EXISTE
        //CRIAR TOKEN
        echo "usuario encontrado";
        $token = sha1(uniqid().date('d-m-Y-H-i-s'));
        //ATUALIZAR O TOKEN DO USUARIO NO BANCO
        $sql = $pdo->prepare("UPDATE usuarios SET token=? WHERE email=? AND senha=?");
        if($sql->execute([$token,$email,$senha_cript])){
            //ARMAZENA ESTE NA SESSAO (SESSIO)
            $_SESSION['token'] = $token;
            header('location: restrita.php');
        }
    }else{
        //USUARIO NÃO EXISTE
        $erro_login = "Usuário ou senha incorretos!";

    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilo.css">
    
    <title>Login</title>
</head>

<body>
    <form method="POST">
        <h1>Login</h1>

        
        <?php 
            if(isset($_GET['result']) and $_GET['result'] == 'ok'){
                echo "<div class='sucesso'>Cadastrado com sucesso</div>";
            }
        ?>
        <div class="input-group">
            <img class="input-icon" src="imgs/login.png">
            <input type="email" name="email" id="" placeholder="Digite seu email" required>
        </div>
        <div class="input-group">
            <img class="input-icon" src="imgs/password.png" >
            <input type="password" name="senha" id="" placeholder="Digite sua senha" required>
        </div>
        <button class="btn-blue" type="submit">Fazer login</button>
        <a href="cadastrar.php">Ainda não tenho cadastro</a>
    </form>
</body>

</html>