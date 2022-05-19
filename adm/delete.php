<?php require_once('alberione.php');
  $id = $_POST['id'];
  $deleteSQL = sprintf("DELETE FROM artigos WHERE id='$id'");
  $Result1 = mysqli_query($alberione, $deleteSQL) or die(mysqli_error());
  header("Location: home.php?excluido=true");