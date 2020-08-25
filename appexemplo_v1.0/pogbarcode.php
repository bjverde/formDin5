<?php

function getCurrentUrl() {
    $pageURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
    $pageURL = $pageURL.$_SERVER["SERVER_NAME"];
    $pageURL = $pageURL.( ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "") ;
    $pageURL = $pageURL.$_SERVER["REQUEST_URI"];
    $url = explode('pogbarcode.php', $pageURL);
    return $url[0];
}

//Evita Notices
if ( is_array($_REQUEST) && array_key_exists('codigo', $_REQUEST) ) {
    $code = $_REQUEST['codigo'];
}

$url = getCurrentUrl();
$url = $url.'index.php?class=exe_bar_code&barcode='.$code;
header('Location: '.$url);
