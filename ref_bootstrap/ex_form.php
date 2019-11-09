<?php 

if(!defined('ENCODINGS') ) { define('ENCODINGS', 'UTF-8'); 
}

require_once '../base/core/webform/TElement.class.php';
require_once '../base/core/webform/TControl.class.php';
require_once '../base/core/webform/TButton.class.php';

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../aa_libs/bootstrap/favicon.ico">
    <title>Exemplo Menu Side</title>


    <link rel="stylesheet" type="text/css" href="../base/lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../base/lib/font-awesome/css/fa-brands.min.css">
    <link rel="stylesheet" type="text/css" href="../base/lib/font-awesome/css/fontawesome.min.css">    
    <link rel="stylesheet" type="text/css" href="formCustom.css">    


</head>
<body>

<form>
  <div class="form-group">
    <label for="exampleInputEmail1">Endereço de email</label>
    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Seu email">
    <small id="emailHelp" class="form-text text-muted">Nunca vamos compartilhar seu email, com ninguém.</small>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Senha</label>
    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Senha">
  </div>
  <div class="form-group form-check">
    <input type="checkbox" class="form-check-input" id="exampleCheck1">
    <label class="form-check-label" for="exampleCheck1">Clique em mim</label>
  </div>
  <button type="submit" class="btn btn-primary">Enviar</button>
</form>

	<!-- ================================================== -->
	<!-- Bootstrap core JavaScript -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../base/lib/jquery/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../base/lib/jquery/jquery.min.js"><\/script>')</script>
    <script src="../base/lib/bootstrap/assets/js/vendor/popper.min.js"></script>
    <script src="../base/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../base/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
