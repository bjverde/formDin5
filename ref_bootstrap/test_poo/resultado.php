<?php
require_once 'teste.class.php';
require_once 'teste2.class.php';

function show($msg){
    echo '<hr>';
    echo '<h1>'.$msg.'</h1>';
}

show('Classe teste');
$t = new teste();
$t->testando();

show('Classe teste 2');
$t2 = new teste2;
$t2->testando();