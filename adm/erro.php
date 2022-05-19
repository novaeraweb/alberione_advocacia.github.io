<?php require_once('../Connections/minhavida.php'); ?>
<?php mysql_set_charset('utf8');
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}


if (isset($_POST['login'])) {
  $loginUsername=$_POST['login'];
  $password=$_POST['senha'];
  $MM_fldUserAuthorization = "";
  if ($_POST['login']=='adm') {
  $acesso = "home-adm.php";
} else {
  $acesso = "home.php";
}
  $MM_redirectLoginSuccess = "$acesso";
  $MM_redirectLoginFailed = "erro.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_minhavida, $minhavida);
  
  $LoginRS__query=sprintf("SELECT login, senha FROM login WHERE login=%s AND senha=%s",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $minhavida) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;       

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];  
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <title>Imobiliária Imóveis Minha Vida | Rodovia Castelo Branco - SP</title>
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
      <meta name="classification" content="Imobiliária Imóveis Minha Vida" />
      <meta name="distribution" content="Global" />
      <meta name="rating" content="General" />
      <meta name="language" content="pt-br" />
      <meta name="doc-class" content="Completed" />
      <meta name="doc-rights" content="Public" />


      <!-- facebook -->
      <meta property="og:type" content="website">
      <meta property="og:locale" content="pt_BR">
      <meta property="og:url" content="http://www.imoveisminhavida.com.br/contato.php">
      <meta property="og:title" content="Imóveis Minha Vida | Contato">
      <meta property="og:site_name" content="Imobiliaria Imóveis Minha Vida">
       
      <meta property="og:description" content="Imobiliária Imóveis Minha Vida, entre em contato conosco, teremos o prazer em atendê - los.">
       
      <meta property="og:image" content="http://www.imoveisminhavida.com.br/images/logo.jpg">
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

      <script src="../js/vendor/modernizr-2.6.2.min.js"></script>

      <script src="../js/modernizr.custom.js"></script>
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

            <div class="grid_10" id="contatos">
                <div class="grid_2" id="logo"><br>
                    <img class="logo" src="../images/imobiliaria-imoveis-minha-vida-adm.png" alt="Logo da Imobiliaria Imóveis Minha Vida" title="Imobiliaria Imóveis Minha Vida">
                </div>

            </div> 
</header>
<div id="main" >
          <div class="container">
            <div class="grid_10" itemprop="breadcrumb" id="breadcrumb">
                <a class="hiddenSpellError" href="index.php">Home</a> » Área Administrativa
            </div>
          </div>
</div>
    <div class="container" style="text-align:center;"><br>
        <div class="grid_10" id="box-login">
            <form class="form-adm" name="form1" method="POST" action="<?php echo $loginFormAction; ?>" >
                <div id="erro">
                  <p>Login e/ou Senha inválidos.</p>
                  <p>Tente novamente.</p><br>
                </div>
                <label>Login</label><br>
                <input type="text" name="login" id="login" autofocus required><br><br>
                <label>Senha</label><br>
                <input type="password" name="senha" id="senha" required><br>
                <input type="submit" class="button" value="Entrar">
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
mysql_free_result($rs_imoveis);
?>