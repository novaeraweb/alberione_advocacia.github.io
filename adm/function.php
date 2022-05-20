<?php 
function listaArtigos ($alberione, $ativo) {
 	$query = "SELECT a.* FROM artigos a WHERE a.ativo = 'sim' ORDER BY id DESC";
 	$resultado = mysqli_query($alberione, $query);
 	$total_artigos= mysqli_num_rows($resultado);
 	$artigos = array();
 	while ($array = mysqli_fetch_assoc($resultado)) {
 		$artigo = new Artigo();
 		$artigo->id = $array['id'];
 		$artigo->titulo = $array['titulo'];
 		$artigo->atuacao = $array['atuacao'];
		$artigo->texto = $array['texto'];
		$artigo->imagem = $array['imagem'];
		$artigo->autor = $array['autor'];
		$artigo->data = $array['data'];
		$artigo->ativo = $array['ativo'];
 		array_push($artigos, $artigo);
 	}
 	return $artigos;
	return $total_artigos;
}
function totalArtigos ($alberione, $ativo) {
 	$query = "select a.* FROM artigos a WHERE a.ativo = 'sim' ";
 	$resultado = mysqli_query($alberione, $query);
 	$total_artigos= mysqli_num_rows($resultado);
 	return $total_artigos;
}
function listaArtigo ($alberione, $id, $ativo) {
	$id = mysqli_real_escape_string($alberione, $id);
 	$query = "select a.* FROM artigos a WHERE a.id = $id AND a.ativo = 'sim' ";
 	$resultado = mysqli_query($alberione, $query);
 	$total_artigo= mysqli_num_rows($resultado);
 	$lista_artigo = array();
 	while ($array = mysqli_fetch_assoc($resultado)) {
 		$artigo = new Artigo();
 		$artigo->id = $array['id'];
 		$artigo->titulo = $array['titulo'];
 		$artigo->atuacao = $array['atuacao'];
		$artigo->texto = $array['texto'];
		$artigo->imagem = $array['imagem'];
		$artigo->autor = $array['autor'];
		$artigo->data = $array['data'];
		$artigo->ativo = $array['ativo'];
 		array_push($lista_artigo, $artigo);
 	}
 	return $lista_artigo;
}


function listaTitulos ($alberione, $ativo) {
 	$query = "select a.* FROM artigos a WHERE a.ativo = 'sim' ";
 	$resultado = mysqli_query($alberione, $query);
 	$total_titulos= mysqli_num_rows($resultado);
 	$titulos = array();
 	while ($array = mysqli_fetch_assoc($resultado)) {
 		$titulo = new Artigo();
 		$titulo->id = $array['id'];
 		$titulo->titulo = $array['titulo'];
 		$titulo->atuacao = $array['atuacao'];
		$titulo->texto = $array['texto'];
		$titulo->imagem = $array['imagem'];
		$titulo->autor = $array['autor'];
		$titulo->data = $array['data'];
		$titulo->ativo = $array['ativo'];
 		array_push($titulos, $titulo);
 	}
 	return $titulos;
}
function listaArtigosRelacionados ($alberione, $ativo, $id) {
 	$query = "select a.* FROM artigos a WHERE a.id != $id AND a.ativo = 'sim' ";
 	$resultado = mysqli_query($alberione, $query);
 	$total_artigos= mysqli_num_rows($resultado);
 	$artigos = array();
 	while ($array = mysqli_fetch_assoc($resultado)) {
 		$artigo = new Artigo();
 		$artigo->id = $array['id'];
 		$artigo->titulo = $array['titulo'];
 		$artigo->atuacao = $array['atuacao'];
		$artigo->texto = $array['texto'];
		$artigo->imagem = $array['imagem'];
		$artigo->autor = $array['autor'];
		$artigo->data = $array['data'];
		$artigo->ativo = $array['ativo'];
 		array_push($artigos, $artigo);
 	}
 	return $artigos;
}
$query_rs_artigos = "SELECT * FROM artigos WHERE ativo='sim' ORDER BY id";
$rs_artigos = mysqli_query($alberione,$query_rs_artigos) or die(mysqli_error($alberione));
$row_rs_artigos = mysqli_fetch_assoc($rs_artigos);
$totalRows_rs_artigos = mysqli_num_rows($rs_artigos);
