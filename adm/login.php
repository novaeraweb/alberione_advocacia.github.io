<?php require_once('alberione.php');session_start();
  require_once 'banco-login.php';
  
  $login = strip_tags(trim(addslashes($_POST["login"])));
  $senha = strip_tags(trim(addslashes($_POST["senha"])));
  if (validaUsuario($alberione, $login, $senha)) {
    $_SESSION["logado"] = true;
    $_SESSION["usuario_logado"] = $login;
    header('location:home.php');
  } else {
    header('location:index.php?erro=true');
  }
  die();