<?php header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
require_once "adm/alberione.php";
require_once "adm/function.php";
require_once "adm/class.php";
mysqli_select_db($alberione,$database_alberione);
$ativo = "sim";
$ativo = mysqli_real_escape_string($alberione, $ativo);
$artigos = listaArtigos($alberione,$ativo);
$titulos = listaTitulos($alberione,$ativo);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="description" content="The Page Description">
<style type="text/css">@-ms-viewport{width: device-width;}</style>
<title>Artigos Alberione Advocacia | Botucatu/SP</title>
<link rel="stylesheet" href="css/layers.min.css" media="screen">
<link rel="stylesheet" href="css/font-awesome.min.css" media="screen"> 
<link rel="stylesheet" href="style.css" media="screen">
<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<link rel="author" href="https://www.novaeraweb.com.br"/>
<link rel="canonical" href="https://alberione.com.br/artigos-alberione-advocacia.php" />
<meta property="og:locale" content="pt_BR">
<meta property="og:title" content="Artigos Alberione Advocacia | Botucatu/SP" />
<meta property="og:type" content="website"/>
<meta property="og:url" content="https://alberione.com.br/artigos-alberione-advocacia.php"/>
<meta property="og:image" content="https://alberione.com.br/images/logo-alberione.png" />
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="800">
<meta property="og:image:height" content="600">
<meta property="og:description" content="Alberione Araujo Advocacia - Possuímos equipes preparada para auxilio na busca de seus direitos! Profissionais das áreas trabalhista, previdenciária, tributária e em direito público, atendendo pessoas físicas e jurídicas!" />
<meta property="og:site_name" content="Advocacia Alberione Araújo | Botucatu/SP"/>
<meta property="fb:admins" content="alberionearaujoadvocacia"/>
<link rel="icon" href="favicon.ico">
<link rel="apple-touch-icon" href="img/apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="76x76" href="img/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="120x120" href="img/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="152x152" href="img/apple-touch-icon-152x152.png">
<?php require_once "ads.php" ?>			
</head>
<body class="blog list-style">
<?php require_once "header.php"?>
<main role="main">
			<div id="intro-wrap" class="artigos">
				<div id="intro" class="preload darken" data-autoplay="5000" data-navigation="true" data-pagination="true" data-transition="fadeUp">					
					<div class="intro-item">
						<div class="caption titulo">
							<h1 style="color:#f9f9f9;text-shadow:1px 0.2px 1px black!important;">ARTIGOS</h1>
						</div><!-- caption -->		
					</div>																					
				</div><!-- intro -->
			</div><!-- intro-wrap -->
			<div id="main" class="row">
				<div class="row-content buffer-left buffer-right buffer-bottom clear-after">
					<div class="column nine">
				<?php foreach($artigos as $artigo) {  ?>
					<article class="clear-after">        
						<div class="column three">
							<figure><img src="adm/arquivos/<?=$artigo->imagem?>" alt="<?=$artigo->titulo?>"style="height:200px;"></figure>
						</div><!-- column three -->
						<div class="column nine last">
							<h2 style="font-size:1.2em"><a href="artigo.php?id=<?=$artigo->id?>"><?=$artigo->titulo?></a></h2>
							<h5 class="meta-post"><?=$artigo->atuacao?></a> - <time datetime="2013-11-10"><?=$artigo->data?></time></h5>
							<p style="text-align:justify;"><?=substr($artigo->texto, 0, strrpos(substr($artigo->texto, 0, 160), ' ')) . '...'; ?> <a href="artigo.php?id=<?=$artigo->id?>">[ leia o artigo completo ]</a></p>

							<p></p>
						</div><!-- column nine -->
					</article>
					<!-- 						<div id="pagination">
					<ul class="clear-after reset plain">
					<li id="older" class="pagination-nav"><a href="#" class="button transparent aqua"><i class="fa fa-chevron-left"></i><span class="label">Older</span></a></li> 
					<li id="newer" class="pagination-nav"><a href="#" class="button transparent aqua"><span class="label">Newer</span><i class="fa fa-chevron-right"></i></a></li>  
					</ul>
					</div>		 -->	
				<?php } ?>								
					</div><!-- column nine -->
					<aside role="complementary" class="sidebar column three last">
<!-- 						<div class="widget widget_search">
							<form role="search">
								<span class="pre-input"><i class="icon icon-search"></i></span>
								<input type="text" placeholder="Search..." value="" class="plain buffer">
							</form>
						</div> -->
						<div class="widget">
							<h4>Sobre o Blog</h4>
							<div class="textwidget">
								Esse é um espaço dedicado a informações e dicas muito relevantes no mundo da advocacia.
							</div><!-- la class="textwidget" è forse generata automaticamente da wp -->
						</div>
						<div class="widget">
							<h4>Todos os artigos</h4>
							<ul class="plain">

        <?php  foreach($titulos as $titulo) {  ?>
                <li><a href="artigo.php?id=<?=$artigo->id?>">- <?= $titulo->titulo?></a></li><br>
        <?php } ?>
							</ul>
						</div>						
					</aside>
				</div><!-- row-content -->
			</div><!-- row -->
		</main><!-- main -->

<?php require_once "footer.php" ?>
<script src="https://code.jquery.com/jquery.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>		
<script src="js/plugins.js"></script>
<script src="js/beetle.js"></script>
</body>
</html>