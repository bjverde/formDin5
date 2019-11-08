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

<?php
echo 'xxx';
echo '<br>';
$btn = new TButton('btnGravar','Gravar',null);
$btn->setEnabled(false);
$btn->show();
echo '<br>';
echo '<br>';
$btn = new TButton('btnGravar','Gravar',null);
$btn->show();
echo '<br>';
echo '<br>';
$btn = new TButton('Exemplo 09 - img', 'zzz', 'act09', null, null, '../base/imagens/joia.gif', '../base/imagens/joia_desabilitado.gif');
$btn->show();
echo '<br>';
echo '<br>';
$btn = new TButton('Exemplo 09 - img', null, 'act09', null, null, '../base/imagens/joia.gif', '../base/imagens/joia_desabilitado.gif');
$btn->setEnabled(false);
$btn->show();
echo '<br>';
echo '<br>';
$btn = new TButton('AAA', 'BBB','CCC','DDD');
$btn->show();

echo '<br>';
echo '<br>';
$btn = new TButton('AAA', 'BBB','CCC','DDD');
$btn->setClass('btn btn-success');
$btn->show();
echo '<br>';
echo '<br>';
$btn = new TButton('AAA', 'BBB','CCC','DDD');
$btn->setClass('btn btn-danger');
$btn->show();

?>
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
