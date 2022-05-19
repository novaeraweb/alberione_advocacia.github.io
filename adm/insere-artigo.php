<?php require_once "checkLogin.php"; require_once"alberione.php";mysqli_select_db($alberione, 'alberione');
    $titulo = $_POST['titulo'];
    $titulo = trim($titulo);

    $atuacao = $_POST['atuacao'];
    $atuacao = trim($atuacao);


    $texto = $_POST['texto'];
    $texto = trim($texto);


    $autor = "Alberione Araújo";

    $data = $_POST['data'];
    $data = trim($data);

    $imagem = $_FILES["imagem"]; 
    $imagem = $imagem["name"];

    $ativo = "SIM";

    $dir = "arquivos/";
    $dir = strtolower($dir);

if ( empty($imagem)) {
  $imagem_novo = NULL;
} else {
    // Descobre a extensao:
    $ext = pathinfo($imagem, PATHINFO_EXTENSION);
    $ponto = ".";
    // Gera novo nome
    $imagem_novo = $hora.'1'.$ponto.$ext;
    move_uploaded_file($_FILES['imagem']['tmp_name'], $dir.$imagem_novo); //Fazer upload do arquivo
}


$insertSQL = "INSERT INTO artigos (titulo, atuacao, texto, autor, data, imagem, ativo) VALUES ('$titulo', '$atuacao', '$texto', '$autor', '$data', '$imagem_novo', '$ativo')";                 
$Result = mysqli_query($alberione, $insertSQL ) or die(mysqli_error($alberione));  
header("Location: home.php?inserido=true");