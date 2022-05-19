<?php require_once('alberione.php');
require_once "checkLogin.php";
require_once "function.php";
require_once "class.php";
mysqli_select_db($alberione,$database_alberione);
$id = $_POST['id'];
$query_rs_login = "SELECT * FROM login WHERE login =$id ";
$rs_login = mysqli_query($alberione,$query_rs_login) or die(mysqli_error());
$row_rs_login = mysqli_fetch_assoc($rs_login);
$totalRows_rs_login = mysqli_num_rows($rs_login);

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <title>Imobiliária Nosso Campo | Pardinho - SP</title>
      <meta name="description" content="Imobiliária Imóveis Minha Vida, entre em contato conosco, teremos o prazer em atendê - los.">
      <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"> 
      <meta name="keywords" content="imobiliaria imoveis minha vida terrenos loteamento fechado rodovia castelo branco imovel imobiliarias pardinho botucatu eco residence ninho verde"/>

    
      <link rel="author" content="Agência Nova Era Web" href="http://www.novaeraweb.com.br"/>
      <meta name="robots" content="follow,index">
      <meta name= "googlebot" content="follow,index">
      <link rel="shortcut icon" href="/favicon.ico" />
        
    <!--[if IE]><link rel="shortcut icon" href="img/favicon.ico"><![endif]-->
      <link rel="icon" href="images/favicon.png">
      <meta name="resource-type" content="document" />
      <meta name="revisit-after" content="1" />
      <meta name="classification" content="Imobiliária Nosso Campo" />
      <meta name="distribution" content="Global" />
      <meta name="rating" content="General" />
      <meta name="language" content="pt-br" />
      <meta name="doc-class" content="Completed" />
      <meta name="doc-rights" content="Public" />


      <!-- facebook -->
      <meta property="og:type" content="website">
      <meta property="og:locale" content="pt_BR">
      <meta property="og:url" content="http://www.imobiliaraialberione.com.br/contato.php">
      <meta property="og:title" content="Imóveis Minha Vida | Contato">
      <meta property="og:site_name" content="Imobiliaria Imóveis Minha Vida">
       
      <meta property="og:description" content="Imobiliária Imóveis Minha Vida, entre em contato conosco, teremos o prazer em atendê - los.">
       
      <meta property="og:image" content="http://www.imobiliariaalberione.com.br/images/logo.jpg">
      <meta property="og:image:type" content="image/jpeg">
      <meta property="og:image:width" content="220"> 
      <meta property="og:image:height" content="164">

      <link rel="stylesheet" href="../css/style.css">
      <link rel="stylesheet" href="../css/normalize.css">
      <link rel="stylesheet" href="../css/main.css">
      <!-- Owl Carousel Assets -->
      <script src="js/jquery-1.9.1.min.js"></script> 
  
      <link href='http://fonts.googleapis.com/css?family=Raleway:300' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="../css/menu.css">

      <script src="../js/script.js"></script>
   <!--[if lt IE 9]>
            <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <![endif]-->
  </head>
 <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
<?php require_once "header.php" ?>
        <div id="main" >
          <div class="container">
            <div class="grid_10" itemprop="breadcrumb" id="breadcrumb">
                <a class="hiddenSpellError" href="home.php">Painel »</a>  Alterar Senha
            </div>
          </div>
        </div>
        <div class="container" id="adm-list">
          <div class="grid_10">
          <form method="POST" action="<?php echo $editFormAction; ?>" name="form1" id="contactform">
             <fieldset>
                 <h3>Alterar Senha</h3><hr>

                        <div class="grid_3">
                              <label>Senha atual  <br>
                                <input type="text" name="login" id="login" value="<?php echo $row_rs_login['senha']; ?>" readonly > 
                              </label>
                        </div>
                        <div class="grid_2">
                              <label>Nova Senha <br>
                                <input type="text" name="senha" id="senha" value=" " > 
                              </label>
                        </div>

                       </fieldset>
          <input type="hidden" name="id" id="id" value="<?php echo $row_rs_login['id']; ?>"> 
          <input type="hidden" name="MM_update" value="form1">                            
          <button style="width:200px" type="button4" id="button" class="btn btn-default"> Alterar Senha</button>
      </form>
      </div>  
        </div>
<footer id="footer-adm" >

    <div class="container" id="autor" > 
         <p>desenvolvido por:</p>
         <p><a href="http://www.novaeraweb.com.br" target="_blank"><img  class="pulse" src="../images/nova-era-web.png" title="Site da Agencia Nova Era Web" alt="Logo Nova Era Web"></a></p>
    </div>
</footer>
    <script src="../assets/js/jquery-1.9.1.min.js"></script> 
    <script src="../js/classie.js"></script>

        <script src="js/jquery.fittext.js"></script>
        <script src="js/jquery.fitvids.js"></script>
        <script type="text/javascript">
            $("h1").fitText(0.62);
            $("h2").fitText(1.7);
        </script>

        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
    </body>
</html><?php
mysql_free_result($rs_imovel);
?>