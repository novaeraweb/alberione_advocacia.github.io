<?php session_start();
if(!$_SESSION['logado'] == true){
header('Location: index.php');
}