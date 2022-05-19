<?php require_once('alberione.php'); 
require_once "checkLogin.php";
session_start();//começa a sessao   

$hora = time();

    $id = $_POST['id'];

	$imagem = $_FILES["imagem"];
	$imagem = $imagem["name"];

	$sql = "UPDATE fotos";

	$arrayCampos = array();
	$diretorio = "arquivos/fotos";
	$diretorio2 = "arquivos/fotos/";

	if($fotos != Null){
	   
		mysqli_select_db($alberione, $database_alberione);
		$query_rs_produto = sprintf("SELECT * FROM fotos WHERE id = $id");
		$rs_produto = mysqli_query($alberione, $query_rs_produto) or die(mysqli_error());
		$row_rs_produto = mysqli_fetch_assoc($rs_produto);
		$rs_arquivo = $row_rs_produto['imagem'];
		
		$id_arquivo = "fotos";
		$nome_arquivo = $_FILES[$id_arquivo]["name"];
		$arquivo_temporario = $_FILES[$id_arquivo]["tmp_name"];
		move_uploaded_file($arquivo_temporario, "$diretorio/$nome_arquivo");
		// Descobre a extensao:
		$ext = pathinfo($fotos, PATHINFO_EXTENSION);
		$ponto = ".";
		// Gera novo nome
		$arquivo_novo = $hora.$ponto.$ext;

		$var = $diretorio2.$fotos ;
		$var_novo = $diretorio2.$arquivo_novo;

		rename($var, $var_novo);
		$arrayCampos[] = "imagem='$arquivo_novo'";

	}
 if( sizeof( $arrayCampos ) ) {

$sql .= ' SET '.implode( ', ',$arrayCampos );
$sql .= ' WHERE id='.$id;
}
// var_dump($sql);

$Result = mysqli_query($alberione, $sql) or die(mysqli_error()); 
header("Location: foto.php?id=$id");