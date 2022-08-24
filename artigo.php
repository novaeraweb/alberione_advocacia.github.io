<?php header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
require_once "adm/alberione.php";
require_once "adm/function.php";
require_once "adm/class.php";
mysqli_select_db($alberione,$database_alberione);
$ativo = "sim";
$id = $_GET['id'];
$id = mysqli_real_escape_string($alberione, $id);
$ativo = mysqli_real_escape_string($alberione, $ativo);
$lista_artigo = listaArtigo($alberione,$id,$ativo);
$total_artigos = totalArtigos($alberione,$ativo);
$artigos_relacionados = listaArtigosRelacionados($alberione,$ativo, $id);
$query_rs_artigo = "SELECT * FROM artigos WHERE id='$id' AND ativo='$ativo' ORDER BY id";
$rs_artigo = mysqli_query($alberione,$query_rs_artigo) or die(mysqli_error($alberione));
$row_rs_artigo = mysqli_fetch_assoc($rs_artigo);
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="The Page Description">
		<style type="text/css">@-ms-viewport{width: device-width;}</style>
		<title>Artigos - Alberione Advocacia | Botucatu/SP</title>
		<link rel="stylesheet" href="css/layers.min.css" media="screen">
		<link rel="stylesheet" href="css/font-awesome.min.css" media="screen"> 
		<link rel="stylesheet" href="style.css" media="screen">
		<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
		<link rel="author" href="https://www.novaeraweb.com.br"/>
		<link rel="canonical" href="https://alberione.com.br/artigo.php?id=<?=$row_rs_artigo['id']?>" />
		<meta property="og:locale" content="pt_BR">
		<meta property="og:title" content="<?= $row_rs_artigo['titulo'] ?>" />
		<meta property="og:type" content="website"/>
		<meta property="og:url" content="https://alberione.com.br/artigo.php?id=<?=$row_rs_artigo['id']?>"/>
		<meta property="og:image" content="https://alberione.com.br/adm/arquivos/<?=$row_rs_artigo['imagem']?>" />
		<meta property="og:image:type" content="image/jpeg">
		<meta property="og:image:width" content="800">
		<meta property="og:image:height" content="600">
		<meta property="og:description" content="<?=$row_rs_artigo['texto']?>" />
		<meta property="og:site_name" content="Advocacia Alberione Araújo | Botucatu/SP"/>
		<meta property="fb:admins" content="alberionearaujoadvocacia"/>
		<link rel="icon" href="favicon.ico">
		<link rel="apple-touch-icon" href="img/apple-touch-icon.png">
		<link rel="apple-touch-icon" sizes="76x76" href="img/apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="120x120" href="img/apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="152x152" href="img/apple-touch-icon-152x152.png">			
	</head>
	<body class="single single-post">
	<?php require_once "header.php" ?>
		<main role="main">
			<?php foreach($lista_artigo as $artigo) {  ?>
			<div id="intro-wrap" class="artigo" style="height:300px;">
				<div id="intro" class="preload darken" data-autoplay="5000" data-navigation="true" data-pagination="true" data-transition="fade">
					<div class="intro-item" style="background-image: url(adm/arquivos/<?=$artigo->imagem?>);">
						<div class="photocaption">
							<h4>Artigo por <?=$artigo->autor?></a></h4>
						</div><!-- photocaption -->
					</div>														
				</div><!-- intro -->				
			</div><!-- intro-wrap -->

			<div id="main" class="row">
				<div class="row-content buffer-left buffer-right buffer-bottom">
					<div id="post-nav">
						<ul class="clear-after reset plain">
							<?php if ($id==1) { ?>
								<li id="prev-items" class="post-nav"><a href="#"><span class="label"></span></a></li>
							<?php } elseif ($id!==1) { ?>
								<li id="prev-items" class="post-nav"><a href="artigo.php?id=<?=$id-1?>"><i style="color:#2C5C7F;" class="fa fa-chevron-left"></i><span class="label" style="color:#2C5C7F;">Artigo Anterior</span></a></li> 
							<?php } ?>

							<li id="all-items" class="post-nav"><a href="artigos-alberione-advocacia.php"><i class="icon icon-images"></i></a></li>
							<?php if ($id==$total_artigos) { ?>
								<li id="prev-items" class="post-nav"><a href="#"><span class="label"></span></a></li>
							<?php } elseif ($id!=$total_artigos) { ?>
								<li id="next-items" class="post-nav"><a href="artigo.php?id=<?=$id+1?>"><span class="label" style="color:#2C5C7F;">Próximo Artigo</span><i style="color:#2C5C7F;" class="fa fa-chevron-right"></i></a></li>  
							<?php } ?> 							 
						</ul>
					</div>
					<div class="post-area clear-after">
						<article role="main">
							<h5 class="meta-post"><a href="#"><?= $artigo->atuacao?></a> - <time datetime="<?=$artigo->titulo?>"><?= $artigo->data?></time></h5>
							<h1><?= $artigo->titulo?></h1>
							<p><?= nl2br($artigo->texto) ?></p>
						</article>

					</div><!-- post-area -->
					<?php } ?>	
<!-- 					<div class="meta-social">
						<ul class="inline center">
							<li><a href="#" class="youtube-share border-box"><i class="fa fa-youtube fa-lg"></i></a></li>
							<li><a href="#" class="facebook-share border-box"><i class="fa fa-facebook fa-lg"></i></a></li>
						</ul>
					</div> -->

					<div class="related clear-after">
						<h4>Veja também</h4>
						<?php $i =0; foreach($artigos_relacionados as $artigo) {  ?>
						<div class="item">
							<figure><img src="adm/arquivos/<?=$artigo->imagem?>" alt=""></figure>
							<a class="overlay" href="artigo.php?id=<?=$artigo->id?>">
								<div class="overlay-content">
									<div class="post-type"><i class="icon icon-search"></i></div>
									<h2><?= $artigo->titulo?></h2>
									<p><?= substr($artigo->texto, 0, strrpos(substr($artigo->texto, 0, 160), ' ')) . '...'; ?></p>
								</div><!-- overlay-content -->
							</a><!-- overlay -->								
						</div>
					<?php  if (++$i == 3) break; } ?>	
					</div>													
				</div><!-- row-content -->
			</div><!-- row -->
		</main><!-- main -->
<?php require_once "footer.php"?>
<script src="https://code.jquery.com/jquery.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>		
<script src="js/plugins.js"></script>
<script src="js/beetle.js"></script>
</body>
</html>