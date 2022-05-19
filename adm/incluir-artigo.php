<?php header('Content-Type: text/html; charset=utf-8');
require_once "checkLogin.php";
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
require_once "alberione.php";
require_once "function.php";
require_once "class.php";
mysqli_select_db($alberione,$database_alberione);
$ativo = "sim";
$artigos = listaArtigos($alberione,$ativo);
$titulos = listaTitulos($alberione,$ativo);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="The Page Description">
    <style type="text/css">@-ms-viewport{width: device-width;}</style>
    <title>Área Adminsitrativa | Alberione Araújo Advogados | Botucatu/SP</title>
    <link rel="stylesheet" href="../css/layers.min.css" media="screen">
    <link rel="stylesheet" href="../css/font-awesome.min.css" media="screen"> 
    <link rel="stylesheet" href="../style.css" media="screen">
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>
    <style>
      textarea {width:100%;max-width:100%;height:400px;}
    </style>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <link rel="icon" href="favicon.ico">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">   
  </head>
  <body class="page">
  <?php require_once "header.php"?>
    <main role="main">
      <div id="intro-wrap" class="servidor-publico" style="height:10em;">
      </div><!-- intro-wrap -->
<div id="main" >

    <div class="grid_10" itemprop="breadcrumb" id="breadcrumb" style="margin-left: 30px; ">
      Área Administrativa » Postar Artigo<a href="home.php"> <button style="font-size: 0.9em;width:300px;height:50px; float: right;margin-top: -5px; background-color:#2C5C7D;color:white;">ÍNICIO</button></a>
    </div>

  <div class="grid_10" style="text-align: center;font-size: 1.3em;padding-top:50px;"><hr></div>

        <div class="container" style="background-color:white;width:100%;">
       
          <form style="width:80%;padding-bottom:80px;" method="POST" enctype="multipart/form-data" action="insere-artigo.php" name="form1" id="contactform">
             <fieldset>
                <h3>Artigo</h3>
                  <div class="grid_7">
                      <label>Título</label>
                      <input type="text" name="titulo" id="titulo" required>
                  </div>
                  <div class="grid_3">
                      <label>Área Atuação</label>
                      <select type="text" name="atuacao" id="atuacao" required>
                        <option selected="">Selecione</option>
                        <option value="Direito Trabalhista">Direito Trabalhista</option>
                        <option value="Direito Tributário">Direito Tributário</option>
                        <option value="Direito Previdenciário">Direito Previdenciário</option>
                        <option value="Servidor Público">Servidor Público</option>
                      </select>
                  </div>
            </fieldset>
            <fieldset>
               <div class="grid_10" style="text-align: left;">
                  <label>Texto</label>
                    <textarea type="text" name="texto" id="texto" rows="28" required></textarea>
               </div> 
              </fieldset>
            <fieldset> 
              <h3>Imagem Capa</h3><hr>
              <div class="grid_10 seleciona-foto">
                  <label for="imagem">Selecione a imagem</label> 
                  <input type="file" name="imagem" id="imagem"  > 
              </div>
            </fieldset>
          <input type="hidden" name="MM_insert" value="form1">
          <input type="hidden" name="data" id="data" value="<?= strftime('%d/%m/%Y', strtotime('today'));?>">                           
          <button type="submit" style="font-size: 1em;width:300px;height:50px; float: right;margin-top: -5px; background-color:#2C5C7D;color:white;"> Incluir </button>
      </form>
   
</div>
</main><!-- main -->
<?php require_once "footer.php" ?>
<script src="https://code.jquery.com/jquery.js"></script>
</body>
</html>

<script>
  setTimeout(function() {
$('#autofade').fadeOut('slow');}, 5000);
</script>
<script src="../js/classie.js"></script>
<script src="js/jquery.fittext.js"></script>
<script src="js/jquery.fitvids.js"></script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>
</body></html>