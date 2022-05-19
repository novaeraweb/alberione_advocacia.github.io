<?php require_once('alberione.php');
require_once "checkLogin.php";
require_once "function.php";
require_once "class.php"; 
if (array_key_exists('id', $_GET) ) {
  $id = $_GET['id'];
} else {
    $id = $_POST['id'];
}
mysqli_select_db($alaberione,$database_alaberione);
$query_rs_artigo = "SELECT * FROM artigos WHERE id = '$id' ";
$rs_artigo = mysqli_query($alaberione,$query_rs_artigo) or die(mysqli_error());
$row_rs_artigo = mysqli_fetch_assoc($rs_artigo);
$totalRows_rs_artigo = mysqli_num_rows($rs_artigo);
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <title>Imobiliária Imóveis Ninho Verde | Rodovia Castelo Branco - SP</title>
      <meta name="description" content="Imobiliária Imóveis Ninho Verde entre em contato conosco, teremos o prazer em atendê - los.">
      <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"> 
      <meta name="keywords" content="imobiliaria imoveis minha vida terrenos loteamento fechado rodovia castelo branco imovel imobiliarias pardinho botucatu eco residence ninho verde"/>
      <link rel="author" content="Agência Nova Era Web" href="http://www.novaeraweb.com.br"/>
      <meta name="robots" content="follow,index">
      <meta name= "googlebot" content="follow,index">
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
      <link rel="stylesheet" href="../css/main.css">
      <!-- Owl Carousel Assets -->
      <script src="js/jquery-1.9.1.min.js"></script>
      <link rel="stylesheet" href="../css/menu.css">
      <script src="../js/script.js"></script>
      <script src="../js/modernizr.custom.js"></script>
   <!--[if lt IE 9]>
            <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <![endif]-->
  </head>
  <script>
    $(document).ready(function() {
      $('form').submit(function(){
        $('#aguarde, #blanket').css('display','block');
    });
});
</script>
 <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
<?php require_once "header.php" ?>
  <div id="main" >
    <div class="container">
      <div class="grid_10" itemprop="breadcrumb" id="breadcrumb">
          <a class="hiddenSpellError" href="home.php">Painel »</a>  Alterar Foto
      </div>
    </div>
  </div>
<div class="grid_10"><br>
    <div id="blanket"></div>
    <div id="aguarde">Aguarde... Estamos efetivando seu cadastro</div> 
    <div class="container" style="margin-top: 70px;">
      <h2>Dados do Imóvel</h2>
      <p><strong><?= $row_rs_imovel['tipo']; ?></strong></p>    
      <p>Finalidade:<strong> <?= $row_rs_imovel['finalidade'];?></strong></p>
      <p>Rua:<strong> <?= $row_rs_imovel['rua'];?></strong></p>
      <p>Cidade:<strong> <?= $row_rs_imovel['cidade'];?></strong></p><br><hr><br>
      <h2>Fotos do Imóvel</h2>
      <div class="grid_10"><br>
        <form name="form" enctype="multipart/form-data" action="altera-imagem.php" method="POST"  id="form1">
          <div class="container">  
          <div class="grid_10 seleciona-foto"> 
              <div class="grid_2">         
                <label>Foto 1 (Capa)</label>
                <?php if ($row_rs_imovel['fotos'] == Null) {  ?>
                    <img src="arquivos/sem-imagem.png" alt="" width="130" height="100" border="0" />             
                <?php } else { ?>
                    <img src="arquivos/<?php echo $row_rs_imovel['fotos']; ?>" alt="" width="130" height="100" border="0" /> 
                <?php } ?>           
              </div>  
              <div class="grid_5">
                    <label>Selecione a nova imagem</label>
                      <input type="file" name="fotos" id="fotos"/> 
                    <br><hr>      
              </div>  
          </div>
          </div><!--/container -->
          <div class="container">  
          <div class="grid_10 seleciona-foto"> 
              <div class="grid_2">         
                <label>Foto 2</label>
                <?php if ($row_rs_imovel['fotos2'] == Null) {  ?>
                    <img src="arquivos/sem-imagem.png" alt="" width="130" height="100" border="0" />             
                <?php } else { ?>
                    <img src="arquivos/<?php echo $row_rs_imovel['fotos2']; ?>" alt="" width="130" height="100" border="0" /> 
                <?php } ?>           
              </div>  
              <div class="grid_5">
                    <label>Selecione a nova imagem</label>
                      <input type="file" name="fotos2" id="fotos"  /> 
                    <br><hr>
                    
              </div>  

          </div>
          </div><!--/container -->
          <div class="container">  
          <div class="grid_10 seleciona-foto"> 
              <div class="grid_2">         
                <label>Foto 3</label>
                <?php if ($row_rs_imovel['fotos3'] == Null) {  ?>
                    <img src="arquivos/sem-imagem.png" alt="" width="130" height="100" border="0" />             
                <?php } else { ?>
                    <img src="arquivos/<?php echo $row_rs_imovel['fotos3']; ?>" alt="" width="130" height="100" border="0" /> 
                <?php } ?>         
              </div>  
              <div class="grid_5">
                    <label>Selecione a nova imagem</label>
                      <input type="file" name="fotos3" id="fotos"  /> 
                    <br><hr>
                    
              </div>  

          </div>
          </div><!--/container -->
          <div class="container">  
          <div class="grid_10 seleciona-foto"> 
              <div class="grid_2">         
                <label>Foto 4</label>
                <?php if ($row_rs_imovel['fotos4'] == Null) {  ?>
                    <img src="arquivos/sem-imagem.png" alt="" width="130" height="100" border="0" />             
                <?php } else { ?>
                    <img src="arquivos/<?php echo $row_rs_imovel['fotos4']; ?>" alt="" width="130" height="100" border="0" /> 
                <?php } ?>     
              </div>  
              <div class="grid_5">
                    <label>Selecione a nova imagem</label>
                      <input type="file" name="fotos4" id="fotos"  /> 
                    <br><hr>
                    
              </div>  

          </div>
          </div><!--/container -->
                    <div class="container">  
          <div class="grid_10 seleciona-foto"> 
              <div class="grid_2">         
                <label>Foto 5</label>
                <?php if ($row_rs_imovel['fotos5'] == Null) {  ?>
                    <img src="arquivos/sem-imagem.png" alt="" width="130" height="100" border="0" />             
                <?php } else { ?>
                    <img src="arquivos/<?php echo $row_rs_imovel['fotos5']; ?>" alt="" width="130" height="100" border="0" /> 
                <?php } ?>     
              </div>  
              <div class="grid_5">
                    <label>Selecione a nova imagem</label>
                      <input type="file" name="fotos5" id="fotos"  /> 
                    <br><hr>
                    
              </div>  

          </div>
          </div><!--/container -->
          <div class="container">  
          <div class="grid_10 seleciona-foto"> 
              <div class="grid_2">         
                <label>Foto 6</label>
                <?php if ($row_rs_imovel['fotos6'] == Null) {  ?>
                    <img src="arquivos/sem-imagem.png" alt="" width="130" height="100" border="0" />             
                <?php } else { ?>
                    <img src="arquivos/<?php echo $row_rs_imovel['fotos6']; ?>" alt="" width="130" height="100" border="0" /> 
                <?php } ?>     
              </div>  
              <div class="grid_6">
                    <label>Selecione a nova imagem</label>
                      <input type="file" name="fotos6" id="fotos"  /> 
                    <br><hr>
              </div>  
          </div>
          </div><!--/container -->
          <div class="container">  
          <div class="grid_10 seleciona-foto"> 
              <div class="grid_2">         
                <label>Foto 7</label>
                <?php if ($row_rs_imovel['fotos7'] == Null) {  ?>
                    <img src="arquivos/sem-imagem.png" alt="" width="130" height="100" border="0" />             
                <?php } else { ?>
                    <img src="arquivos/<?php echo $row_rs_imovel['fotos7']; ?>" alt="" width="130" height="100" border="0" /> 
                <?php } ?>     
              </div>  
              <div class="grid_7">
                    <label>Selecione a nova imagem</label>
                      <input type="file" name="fotos7" id="fotos"  /> 
                    <br><hr>
                    
              </div>  
          </div>
          </div><!--/container -->
          <div class="container">  
          <div class="grid_10 seleciona-foto"> 
              <div class="grid_2">         
                <label>Foto 8</label>
                <?php if ($row_rs_imovel['fotos8'] == Null) {  ?>
                    <img src="arquivos/sem-imagem.png" alt="" width="130" height="100" border="0" />             
                <?php } else { ?>
                    <img src="arquivos/<?php echo $row_rs_imovel['fotos8']; ?>" alt="" width="130" height="100" border="0" /> 
                <?php } ?>     
              </div>  
              <div class="grid_8">
                    <label>Selecione a nova imagem</label>
                      <input type="file" name="fotos8" id="fotos"  /> 
                    <br><hr>
                    
              </div>  
          </div>
          </div><!--/container -->
          <input name="id" type="hidden" id="id" value="<?php echo $row_rs_imovel['id']; ?>" /> 
        
      </div>
      <div class="grid_10"><br>
        <p><a href="home.php" class="btn-voltar">< Voltar</a><button type="submit" class="btn-salvar">Salvar</button></p>
      </div>
    </form>
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
</body></html>