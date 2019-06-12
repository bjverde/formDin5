<?php
echo 'antes <hr>';

require_once '../base/core/webform/TApplication.class.php';

$frm = new TForm('Configurações do PHP');
$frm->setPublicMode(true);
$frm->show();

echo 'depois <hr>';
?>
