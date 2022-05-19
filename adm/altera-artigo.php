<?php header('Content-Type: text/html; charset=utf-8');
require_once "checkLogin.php";
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
require_once "alberione.php";
require_once "function.php";
require_once "class.php";

    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $titulo = trim($titulo);

    $atuacao = $_POST['atuacao'];
    $atuacao = trim($atuacao);

    $texto = $_POST['texto'];
    $texto = trim($texto);

    $data = $_POST['data'];
    $data = trim($data);

$insertSQL = "UPDATE artigos SET titulo='$titulo', atuacao='$atuacao', texto='$texto', data='$data' WHERE id='$id'";

$Result = mysqli_query($alberione, $insertSQL) or die(mysqli_error($alberione));  
header("Location: home.php?cadastro=true");