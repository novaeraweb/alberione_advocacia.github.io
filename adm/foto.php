<?php require_once('../Connections/minhavida.php'); 
require_once "checkLogin.php";ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

mysqli_select_db($minhavida,$database_minhavida);
$query_rs_imoveis = "SELECT * FROM imoveis ORDER BY tipo";
$rs_imoveis = mysqli_query($minhavida,$query_rs_imoveis) or die(mysqli_error());
$row_rs_imoveis = mysqli_fetch_assoc($rs_imoveis);
$totalRows_rs_imoveis = mysqli_num_rows($rs_imoveis);

mysqli_select_db($minhavida,$database_minhavida);
$query_rs_terrenos = "SELECT * FROM imoveis WHERE tipo='terreno' ORDER BY id";
$rs_terrenos = mysqli_query($minhavida,$query_rs_terrenos) or die(mysqli_error());
$row_rs_terrenos = mysqli_fetch_assoc($rs_terrenos);
$totalRows_rs_terrenos = mysqli_num_rows($rs_terrenos);

mysqli_select_db($minhavida,$database_minhavida);
$query_rs_casas = "SELECT * FROM imoveis WHERE tipo='casa' ORDER BY id";
$rs_casas = mysqli_query($minhavida,$query_rs_casas) or die(mysqli_error());
$row_rs_casas = mysqli_fetch_assoc($rs_casas);
$totalRows_rs_casas = mysqli_num_rows($rs_casas);

mysqli_select_db($minhavida,$database_minhavida);
$query_rs_locacao = "SELECT * FROM imoveis WHERE tipo='casa de campo' ORDER BY id";
$rs_locacao = mysqli_query($minhavida,$query_rs_locacao) or die(mysqli_error());
$row_rs_locacao = mysqli_fetch_assoc($rs_locacao);
$totalRows_rs_locacao = mysqli_num_rows($rs_locacao);

mysqli_select_db($minhavida,$database_minhavida);
$query_rs_videos = "SELECT * FROM videos WHERE ativo='sim' ORDER BY id";
$rs_videos = mysqli_query($minhavida,$query_rs_videos) or die(mysqli_error());
$row_rs_videos = mysqli_fetch_assoc($rs_videos);
$totalRows_rs_videos = mysqli_num_rows($rs_videos);

mysqli_select_db($minhavida,$database_minhavida);
$query_rs_familia = "SELECT * FROM fotos WHERE ativo='sim' AND categoria='familia' ORDER BY id";
$rs_familia = mysqli_query($minhavida,$query_rs_familia) or die(mysqli_error());
$row_rs_familia = mysqli_fetch_assoc($rs_familia);
$totalRows_rs_familia = mysqli_num_rows($rs_familia);

mysqli_select_db($minhavida,$database_minhavida);
$query_rs_regiao = "SELECT * FROM fotos WHERE ativo='sim' AND categoria='regiao' ORDER BY id";
$rs_regiao = mysqli_query($minhavida,$query_rs_regiao) or die(mysqli_error());
$row_rs_regiao = mysqli_fetch_assoc($rs_regiao);
$totalRows_rs_regiao = mysqli_num_rows($rs_regiao);

mysqli_select_db($minhavida,$database_minhavida);
$query_rs_construcao = "SELECT * FROM fotos WHERE ativo='sim' AND categoria='construcao' ORDER BY id";
$rs_construcao = mysqli_query($minhavida,$query_rs_construcao) or die(mysqli_error());
$row_rs_construcao = mysqli_fetch_assoc($rs_construcao);
$totalRows_rs_construcao = mysqli_num_rows($rs_construcao);

mysqli_select_db($minhavida,$database_minhavida);
$query_rs_cliente = "SELECT * FROM fotos WHERE ativo='sim' AND categoria='cliente' ORDER BY id";
$rs_cliente = mysqli_query($minhavida,$query_rs_cliente) or die(mysqli_error());
$row_rs_cliente = mysqli_fetch_assoc($rs_cliente);
$totalRows_rs_cliente = mysqli_num_rows($rs_cliente);
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
<link rel="stylesheet" href="../css/normalize.css">
      <link rel="stylesheet" href="../css/style.css">
      <link rel="stylesheet" href="../css/main.css">
      <link rel="stylesheet" href="../css/menu.css">
      <script src="../js/script.js"></script>
      <script src="../js/jquery-1.9.1.min.js"></script>
   <!--[if lt IE 9]>
            <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <![endif]-->
  </head>
 <body>
<?php require_once "header.php" ?><?php require_once "../alerta.php" ?>
<div id="main" >
  <div class="container">
    <div class="grid_10" itemprop="breadcrumb" id="breadcrumb">
      <a class="hiddenSpellError" href="home.php">Painel</a> » Fotos
    </div>
  </div>
</div>
<div class="container" id="adm-list">
  <input id="tab1" type="radio" name="tabs" checked>
  <label for="tab1" style="padding:20px 20px;">Fotos Família</label>
  <input id="tab2" type="radio" name="tabs">
  <label for="tab2" style="padding:20px 20px;">Fotos Região</label>
  <input id="tab3" type="radio" name="tabs">
  <label for="tab3" style="padding:20px 20px;">Fotos Construção</label>
  <input id="tab4" type="radio" name="tabs">
  <label for="tab4" style="padding:20px 20px;">Fotos Cliente</label>
  <section class="none" id="content1">
    <?php do { ?>
        <table width="1150" border="0">
          <tr>
            <td width="90" align="left"><div class="img-m"><img src="arquivos/fotos/<?php echo $row_rs_familia['imagem']; ?>" alt="" border="0" /></div></td>
            <td width="280" style="text-align: center;"><?php echo $row_rs_familia['categoria']; ?></td>
            <td width="20" align="right" valign="middle">
              <form action="editar-foto.php" method="post" name="form1" id="form1">
                  <input type="image" name="button" id="button" src="../images/icon-editar-foto.png"  title="Editar Foto" value="Alterar fotos do produto" />
                  <input name="id" type="hidden" id="id" value="<?php echo $row_rs_familia['id']; ?>" size="1" />
              </form>
            </td>
          </tr>
        </table>
    <?php } while ($row_rs_familia = mysqli_fetch_assoc($rs_familia)); ?>
  </section>  
  <section class="none" id="content2">
    <?php do { ?>
        <table width="1150" border="0">
          <tr>
            <td width="90" align="left"><div class="img-m"><img src="arquivos/fotos/<?php echo $row_rs_regiao['imagem']; ?>" alt="" border="0" /></div></td>
            <td width="280" style="text-align: center;"><?php echo $row_rs_regiao['categoria']; ?></td>
            <td width="20" align="right" valign="middle">
              <form action="editar-foto.php" method="post" name="form1" id="form1">
                  <input type="image" name="button" id="button" src="../images/icon-editar-foto.png"  title="Editar Foto" value="Alterar fotos do produto" />
                  <input name="id" type="hidden" id="id" value="<?php echo $row_rs_regiao['id']; ?>" size="1" />
              </form>
            </td>
          </tr>
        </table>
    <?php } while ($row_rs_regiao = mysqli_fetch_assoc($rs_regiao)); ?>
  </section>
  <section class="none" id="content3">
    <?php do { ?>
        <table width="1150" border="0">
          <tr>
            <td width="90" align="left"><div class="img-m"><img src="arquivos/fotos/<?php echo $row_rs_construcao['imagem']; ?>" alt="" border="0" /></div></td>
            <td width="280" style="text-align: center;"><?php echo $row_rs_construcao['categoria']; ?></td>
            <td width="20" align="right" valign="middle">
              <form action="editar-foto.php" method="post" name="form1" id="form1">
                  <input type="image" name="button" id="button" src="../images/icon-editar-foto.png"  title="Editar Foto" value="Alterar fotos da construçao" />
                  <input name="id" type="hidden" id="id" value="<?php echo $row_rs_construcao['id']; ?>" size="1" />
              </form>
            </td>
          </tr>
        </table>
    <?php } while ($row_rs_construcao = mysqli_fetch_assoc($rs_construcao)); ?>
  </section>
    <section class="none" id="content4">
    <?php do { ?>
        <table width="1150" border="0">
          <tr>
            <td width="90" align="left"><div class="img-m"><img src="arquivos/fotos/<?php echo $row_rs_cliente['imagem']; ?>" alt="" border="0" /></div></td>
            <td width="280" style="text-align: center;"><?php echo $row_rs_cliente['categoria']; ?></td>
            <td width="20" align="right" valign="middle">
              <form action="editar-foto.php" method="post" name="form1" id="form1">
                  <input type="image" name="button" id="button" src="../images/icon-editar-foto.png"  title="Editar Foto" value="Alterar fotos do cliente" />
                  <input name="id" type="hidden" id="id" value="<?php echo $row_rs_cliente['id']; ?>" size="1" />
              </form>
            </td>
          </tr>
        </table>
    <?php } while ($row_rs_cliente = mysqli_fetch_assoc($rs_cliente)); ?>
  </section> 
</div>
<footer id="footer-adm" >
    <div class="container" id="autor" > 
         <p>desenvolvido por:</p>
         <p><a href="https://www.novaeraweb.com.br" target="_blank"><img  class="pulse" src="../images/nova-era-web.png" title="Site da Agencia Nova Era Web" alt="Logo Nova Era Web"></a></p>
    </div>
</footer>
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