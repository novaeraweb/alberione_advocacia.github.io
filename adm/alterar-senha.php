<?php session_start();
require_once "checkLogin.php";
require_once "conecta.php";
require_once "class.php";
?>
<!doctype html>
<html lang="pt_BR">
<head>
  <title>Administração Alberione Araújo Advogados | Botucatu/SP</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- Material Kit CSS -->
  <link href="assets/css/material-dashboard.css?v=2.1.2" rel="stylesheet" />
</head>
<body>
  <div class="wrapper ">
    <?php require_once "sidebar.php"?>
    </div>
    <div class="main-panel">
      <!-- Navbar -->
      <?php require_once "dashboard.php"?>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <!-- your content here -->
            <div class="row"><?php require_once "alerta.php"?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-text card-header-info">
                            <div class="card-text">
                                <h4 class="card-title">Alterações cadastrais</h4>
                            </div>
                        </div>
                        <div class="card-body">
                                Utilize o formulário abaixo para alterar a senha de seu usuário:
                                <p><strong>As novas senhas necessitam de, no mínimo, 6 caracteres</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="card col-md-8" style="padding-top: 20px;">
                    <form  class="col-md-8" method="POST" action="banco-alterar-senha.php">
                        <div class="form-group">
                            <label for="nome">Usuário: </label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="<?php echo $_SESSION["usuario_logado"];?>" disabled>
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION["user_id"];?>">
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="senhaatual">Senha atual:</label>
                            <input type="password" class="form-control" name="senhaatual" id="senhaatual" placeholder="">
                            
                        </div>
                        <div class="form-group" style="margin-top:60px;">
                            <label for="novasenha">Nova Senha:</label>
                            <input type="password" class="form-control" name="novasenha" id="novasenha" placeholder="" minlength="6">
                        </div>
                        <button type="submit" class="btn btn-info float-right">Confirmar</button>
                    </form>
                </div>
            </div>
          </div> 
        </div>
      </div>
      <?php require_once "footer.php"?>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="assets/js/core/jquery.min.js" type="text/javascript"></script>
  <script src="assets/js/core/popper.min.js" type="text/javascript"></script>
  <script src="assets/js/core/bootstrap-material-design.min.js" type="text/javascript"></script>
  <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <script src="assets/js/material-dashboard.js" type="text/javascript"></script>
  <!-- Chartist JS -->
  <script src="assets/js/plugins/chartist.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/material-dashboard.js?v=2.1.2" type="text/javascript"></script>


</body>

</html>