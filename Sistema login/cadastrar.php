<?php
require('db/conexao.php');
if (
    isset($_POST['nome_completo']) and
    isset($_POST['email']) and
    isset($_POST['senha']) and
    isset($_POST['repete_senha'])
) {
    if (
        empty($_POST['nome_completo']) or
        empty($_POST['email']) or
        empty($_POST['senha']) or
        empty($_POST['repete_senha']) or
        empty($_POST['termos'])
    ) {
        $erro_geral = "Todos os campos são obrigatórios";
    } else {
        //RECEBE OS VALORES DO POST E PREVINIR INJEÇÃO DE SQL, HTML, JAVASCRIPT, MELHORA A SEGURANÇA    
        $nome = limparPost($_POST['nome_completo']);
        $email = limparPost($_POST['email']);
        $senha = limparPost($_POST['senha']);
        $senha_cript = sha1($senha);
        $repete_senha = limparPost($_POST['repete_senha']);
        $checkbox = limparPost($_POST['termos']);

        //VERIFICA SE NOME É APENAS LETRAS E ESPAÇOS EM BRANCO
        if (!preg_match("/^[a-zA-Z-' ]*$/", $nome)) {
            $erro_nome = "Somente letras e espaços em branco!";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_erro = "Formato de e-mail inválido";
        }

        //VERFICAR SE A SENHA TEM MAIS DE 6 DÍGITOS
        if (strlen($senha) < 6) {
            $erro_senha = "Senha deve ter no mínimo 6 ou mais caracteres";
        }

        //VERFICAR SE A SENHA É IGUAL A REPETE SENHA

        if ($senha !== $repete_senha) {
            $erro_repete_senha = "Senha e repetição de senha são diferentes";
        }

        //VERIFICAR SE CHECKBOX FOI MARCADO
        if ($checkbox !== "ok") {
            $erro_check_box = "Desativado";
        }

        if (!isset($erro_geral) and 
        !isset($erro_nome) and 
        !isset($email_erro) and 
        !isset($erro_senha) and 
        !isset($erro_repete_senha) and 
        !isset($erro_check_box)) {
            //VERIFICAR SE O USUÁRIO ESTÁ CADASTRADO NO BANCO
            $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?LIMIT 1");
            $sql->execute([$email]);
            $usuario = $sql->fetch();
            if(!$usuario){
                $recupera_senha="";
                $token="";
                $status="novo";
                $data_cadastro = date('d/m/Y');
                $sql = $pdo->prepare("INSERT INTO usuarios VALUES (null,?,?,?,?,?,?,?)");
                if($sql->execute([$nome,$email,$senha_cript,$recupera_senha,$token,$status,$data_cadastro])){
                    header('location: index.php');
                }
            }else{
                //JÁ EXISTE USUÁRIO
                $erro_geral = "Usuário já cadastrado";
            }
        }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <title>Login</title>
</head>

<body>
    <form method="POST">
        <h1>Cadastrar</h1>
        <?php if (isset($erro_geral)) { ?>
            <div class="erro-geral animate__animated animate__rubberBand">
                <?php echo $erro_geral; ?>
            </div>
        <?php } ?>
        <div class="input-group">
            <img class="input-icon" src="imgs/id.png">
            <input <?php if (isset($erro_geral) or isset($erro_nome)) {
                        echo "class='erro-input'";
                    } ?> type="text" name="nome_completo" id="" placeholder="Nome" <?php if (isset($nome)) {
                                                                                        echo "value='$nome'";
                                                                                    } ?> required>
            <?php
            if (isset($erro_nome)) {
                echo "<div class='erro'>$erro_nome</div>";
            }
            ?>
        </div>
        <div class="input-group">
            <img class="input-icon" src="imgs/login.png">
            <input <?php if (isset($erro_geral) or isset($email_erro)) {
                        echo "class='erro-input'";
                    } ?> type="email" name="email" id="" placeholder="Email" <?php if (isset($email)) {
                                                                                    echo "value='$email'";
                                                                                } ?> required>
            <?php
            if (isset($email_erro)) {
                echo "<div class='erro'>$email_erro</div>";
            }
            ?>
        </div>
        <div class="input-group">
            <img class="input-icon" src="imgs/password.png">
            <input <?php if (isset($erro_geral) or isset($erro_senha)) {
                        echo "class='erro-input'";
                    } ?> type="password" name="senha" id="" placeholder="Senha mínimo 6 dígitos" <?php if (isset($senha)) {
                                                                                                        echo "value='$senha'";
                                                                                                    } ?> required>
            <?php
            if (isset($erro_senha)) {
                echo "<div class='erro'>$erro_senha</div>";
            }
            ?>
        </div>
        <div class="input-group">
            <img class="input-icon" src="imgs/password.png">
            <input <?php if (isset($erro_geral) or isset($erro_repete_senha)) {
                        echo "class='erro-input'";
                    } ?> type="password" name="repete_senha" id="" placeholder="Repita a senha" <?php if (isset($repete_senha)) {
                                                                                                    echo "value='$nome'";
                                                                                                } ?> required>
            <?php
            if (isset($erro_repete_senha)) {
                echo "<div class='erro'>$erro_repete_senha</div>";
            }
            ?>
        </div>
        <div <?php if (isset($erro_geral) or isset($erro_check_box)) {
                    echo "class='erro-input input-group'";
                } ?>>
            <input type="checkbox" name="termos" id="termos" value="ok" required>
            <label for="termos">Ao se cadastrar você concorda com a nossa <a class="link" href="#">política de
                    privacidade </a> e os <a class="link" href="#">termos de uso </a></label>
        </div>
        <button class="btn-blue" type="submit">Fazer login</button>
        <a href="index.php">Já tenho uma conta</a>
    </form>
</body>

</html>