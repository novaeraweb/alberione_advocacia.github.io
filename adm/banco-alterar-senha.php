<?php
session_start();
require_once "conecta.php";
mysqli_select_db($alberione, $database_alberione);

$user_id = $_SESSION["user_id"];
$senha = $_SESSION['user_senha'];
$senhaAtual = $_POST['senhaatual'];
$senhaAtual = md5($senhaAtual);
$novasenha = $_POST['novasenha'];
$senhaMd5 = md5($novasenha);


if ($senhaAtual === $senha){
    $query = "UPDATE login SET senha = '$senhaMd5' WHERE id='$user_id'";
    $resultado = mysqli_query($alberione, $query) or die(mysqli_error($alberione));  
    // $usuario = mysqli_fetch_assoc($resultado);
    $_SESSION["user_senha"] = $senhaMd5;
}  else {
    echo "Senha atual incorreta";
}

	

header("Location: home.php?alterado=true");