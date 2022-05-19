<?php require_once('alberione.php');?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <title>Alberione Araujo Advocacia | Botucatu/SP</title>
      <meta name="description" content="Imobiliária Imóveis Minha Vida, entre em contato conosco, teremos o prazer em atendê - los.">
      <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"> 
      <link rel="author" content="Agência Nova Era Web" href="http://www.novaeraweb.com.br"/>
      <meta name="robots" content="nofollow,noindex">
      <meta name= "googlebot" content="nofollow,noindex">
      <link rel="shortcut icon" href="/favicon.ico" />
      <link rel="icon" href="images/favicon.png">
      <meta name="resource-type" content="document" />
      <meta name="revisit-after" content="1" />
      <meta name="classification" content="Imobiliária Imóveis Minha Vida" />
      <meta name="distribution" content="Global" />
      <meta name="rating" content="General" />
      <meta name="language" content="pt-br" />
      <meta name="doc-class" content="Completed" />
      <meta name="doc-rights" content="Public" />
      <link rel="stylesheet" href="../css/style.css">
      <link rel="stylesheet" href="../css/normalize.css">
      <link rel="stylesheet" href="../css/main.css">
      <link rel="stylesheet" href="../css/menu.css">
   <!--[if lt IE 9]>
            <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <![endif]-->
  </head>
 <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
<header>
<div class="grid_10" >
  <nav id='cssmenu'>
    <ul>
        <li ><a href='#'><span></span></a>
        </li>
        <li><a href='#'><span></span></a>
        </li>
    </ul>
  </nav>
</div>
  <div class="grid_10" style="text-align: center;">
      <img src="../images/imobiliaria-nosso-campo.png" alt="Logo da Imobiliaria Nosso Campo" title="Imobiliaria Nosso Campo" style="width: 15%;">
  </div> 
</header>
<div id="main" style="background-image: url(../images/crossword.png);" >
  <div class="container" style="text-align:center;"><br><br>
      <div class="grid_10" id="box-login">
        <?php if (array_key_exists('erro', $_GET) && $_GET['erro']=='true') { ?>
          <div id="erro">
            <p> Login e Senha não conferem!<br>Tente novamente. </p>
          </div>
        <?php } ?>
        <form class="form-adm" name="form1" method="POST" action="login.php" >
            <label>Login</label><br>
            <input type="text" name="login" id="login" autofocus required><br><br>
            <label>Senha</label><br>
            <input type="password" name="senha" id="senha" required><br>
            <input type="submit" class="button" name="bt-login" value="Entrar">
        </form>
      </div>
  </div>
</div>
<footer id="footer-adm" >
  <div class="container" id="autor" ><hr> 
     <p>desenvolvido por:</p>
     <p><a href="https://www.novaeraweb.com.br" target="_blank"><img  class="pulse" src="../images/nova-era-web.png" title="Site da Agencia Nova Era Web" alt="Logo Nova Era Web"></a></p>
  </div>
</footer>
<script src="../js/classie.js"></script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>
</body></html>