<?php
    session_start();
    require_once("conexao.php");

    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $admin = $conn->real_escape_string($_POST['admin']);
    $senha = base64_encode($conn->real_escape_string($_POST['senha']));
    $senhaRepetida = base64_encode($conn->real_escape_string($_POST['senhaRepetida']));

    if($senha != $senhaRepetida){
        exit("ERRO");
    }else{
        
        $result_usuario = "INSERT INTO usuarios(nome, email, senha, admin) VALUES ('$nome','$email', '$senha', '$admin')";
        $resultado_usuario = mysqli_query($conn, $result_usuario);
        
        if(mysqli_affected_rows($conn) != 0){
            exit('success');   
        }else{

            exit("erro");  
        }
    }

?>