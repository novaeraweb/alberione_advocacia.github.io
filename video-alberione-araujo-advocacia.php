<!DOCTYPE html>
<?php

// API config 
$API_Key    = 'AIzaSyDaSQlsKyqIHD7r9wBDZ6OH8uyi19RJSng'; 
$Channel_ID = 'UCHt6Zkkg5zMKvrL2Zi3hXTg'; 
$Max_Results = 10; 
 
// Get videos from channel by YouTube Data API 
$apiData = @file_get_contents('https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId='.$Channel_ID.'&maxResults='.$Max_Results.'&key='.$API_Key.''); 
if($apiData){ 
    $videoList = json_decode($apiData); 
}else{ 
    echo 'Invalid API key or channel ID.'; 
}

?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="Vídeos do nosso canal do YouTube | Alberione Araújo Advogados | Botucatu/SP.">
		<style type="text/css">@-ms-viewport{width: device-width;}</style>
		<title>Vídeos | Alberione Araújo Advogados | Botucatu/SP</title>
		<link rel="stylesheet" href="css/layers.min.css" media="screen">
		<link rel="stylesheet" href="css/font-awesome.min.css" media="screen"> 
		<link rel="stylesheet" href="style.css" media="screen">
		<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>
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
			<div id="intro-wrap" class="tributario">
				<div id="intro" class="preload darken" data-autoplay="5000" data-navigation="true" data-pagination="true" data-transition="fadeUp">					
					<div class="intro-item">
						<div class="caption titulo">
							<h1 style="color:#f9f9f9;text-shadow:1px 0.2px 1px black!important;">Vídeos</h1>
						</div><!-- caption -->		
					</div>														
															
				</div><!-- intro -->

			</div><!-- intro-wrap -->

			<div id="main">

				<section class="row section call-to-action">
					<div class="row-content buffer even">
						<p style="text-align: justify;">Confira os vídeos do nosso canal do YouTube:
						</p>

					<?php
						if(!empty($videoList->items)){ 
							foreach($videoList->items as $item){ 
											// Embed video 
											if(isset($item->id->videoId)){ 
															echo ' 
															<div class="yvideo-box"> 
																			<iframe width="400" height="250" src="https://www.youtube.com/embed/'.$item->id->videoId.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> 
																			<h3>'. $item->snippet->title .'</h3>
																			<br>
															</div>'; 
											} 
							} 
						}else{ 
										echo '<p class="error">'.$apiError.'</p>'; 
						}
					?>

						<iframe width="560" height="350" src="https://www.youtube.com/channel/UCHt6Zkkg5zMKvrL2Zi3hXTg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
				</section>															
				<section class="row section call-to-action">
					<div class="row-content buffer even animation">
						<p><strong>Precisando de ajuda com Direito Tributário?</strong></p><br>
						<p>Possuímos equipes preparada para auxilio na busca de seus direitos!<br>< /p><br><br>
						<a class="button red" href="contato-advocacia.php">Entre em contato</a>
					</div>
				</section>		

<?php require_once "mapa.php" ?>								

			</div><!-- id-main -->
		</main><!-- main -->
		<?php require_once "footer.php" ?>
		<script src="https://code.jquery.com/jquery.js"></script>
		<script src="js/plugins.js"></script>
		<script src="js/beetle.js"></script>
	</body>
</html>