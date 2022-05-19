<?php
$query_rs_casas = "SELECT * FROM imoveis WHERE tipo='Casa' AND finalidade='venda' AND ativo='sim' ORDER BY id";
$rs_casas = mysqli_query($nossocampo,$query_rs_casas) or die(mysqli_error());
$totalRows_rs_casas = mysqli_num_rows($rs_casas);

$query_rs_rural = "SELECT * FROM imoveis WHERE tipo='Rural' AND finalidade='venda' AND ativo='sim' ORDER BY id";
$rs_rural = mysqli_query($nossocampo,$query_rs_rural) or die(mysqli_error());
$totalRows_rs_rural = mysqli_num_rows($rs_rural);

$query_rs_terrenos = "SELECT * FROM imoveis WHERE tipo='Terreno' AND finalidade='venda' AND ativo='sim' ORDER BY id";
$rs_terrenos = mysqli_query($nossocampo,$query_rs_terrenos) or die(mysqli_error());
$totalRows_rs_terrenos = mysqli_num_rows($rs_terrenos);

$query_rs_casas_locacao = "SELECT * FROM imoveis WHERE tipo='Casa' AND finalidade='locação' AND ativo='sim' ORDER BY id";
$rs_casas_locacao = mysqli_query($nossocampo,$query_rs_casas_locacao) or die(mysqli_error());
$totalRows_rs_casas_locacao = mysqli_num_rows($rs_casas_locacao);

$query_rs_apt_locacao = "SELECT * FROM imoveis WHERE tipo='Apartamento' AND finalidade='locação' AND ativo='sim' ORDER BY id";
$rs_apt_locacao = mysqli_query($nossocampo,$query_rs_apt_locacao) or die(mysqli_error());
$totalRows_rs_apt_locacao = mysqli_num_rows($rs_apt_locacao);

$query_rs_ponto_locacao = "SELECT * FROM imoveis WHERE tipo='Ponto Comercial' AND finalidade='locação' AND ativo='sim' ORDER BY id";
$rs_ponto_locacao = mysqli_query($nossocampo,$query_rs_ponto_locacao) or die(mysqli_error());
$totalRows_rs_ponto_locacao = mysqli_num_rows($rs_ponto_locacao);