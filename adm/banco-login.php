<?php 
	 function validaUsuario($alberione, $login, $senha) {
	 	$senha = $senha;
		$senhaMd5 = md5($senha);
	 	$login = $login;
	 	$login = mysqli_real_escape_string($alberione, $login);
	 	$senhaMd5 = mysqli_real_escape_string($alberione, $senhaMd5);
		$tipo = adm;
		$tipo = mysqli_real_escape_string($alberione, $tipo);
	 	$query = "SELECT * FROM login WHERE login ='$login' AND senha='$senhaMd5' AND tipo ='$tipo' " ;
	 	$resultado = mysqli_query($alberione, $query);
	 	$usuario = mysqli_fetch_assoc($resultado);
	 	return $usuario;
	 }
