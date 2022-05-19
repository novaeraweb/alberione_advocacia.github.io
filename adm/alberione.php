<?php $URL_ATUAL= "$_SERVER[SERVER_NAME]";
if (strpos($URL_ATUAL, 'alberione') !== false) {
	$alberione = mysqli_connect('mysql465.umbler.com', 'alberioneadv', 'kpyRjCLQ', 'alberione');
} else {
	$alberione = mysqli_connect('localhost', 'root', 'root', 'alberione');
}
mysqli_query($alberione, "SET NAMES 'utf8'");
mysqli_query($alberione,'SET character_set_connection=utf8');
mysqli_query($alberione,'SET character_set_client=utf8');
mysqli_query($alberione,'SET character_set_results=utf8');
$database_alberione = "alberione";
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');