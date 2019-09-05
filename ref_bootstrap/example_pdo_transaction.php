<?php

$HOST    ='localhost';
$PORT    ='3306';
$DATABASE='form_exemplo';
$USUARIO ='form_exemplo';
$SENHA   ='123456';

try {  
    $config = array ( PDO::ATTR_EMULATE_PREPARES => false
                    , PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    , PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
                    );
    $conn = new PDO('mysql:host='.$HOST.';dbname='.$DATABASE.';charset=utf8',$USUARIO, $SENHA, $config );
} catch (Exception $e) {
    die("Unable to connect: " . $e->getMessage());
}

function getAllAttributes($conn){
    $attributes = array( "AUTOCOMMIT"
                       , "ERRMODE"
                       , "CASE"
                       , "CLIENT_VERSION"
                       , "CONNECTION_STATUS"
                       , "ORACLE_NULLS"
                       , "PERSISTENT"                       
                       , "SERVER_INFO"
                       , "SERVER_VERSION"
                       , "TIMEOUT"
                       , "PREFETCH"
                       );

    foreach ($attributes as $val) {
        echo '<pre>';
        echo "PDO::ATTR_$val: ";
        echo $conn->getAttribute(constant("PDO::ATTR_$val")) . "\n";
        echo '</pre>';
    }
}

$conn->beginTransaction();       
try { 
    //getAllAttributes($conn);
    
    var_dump($conn->inTransaction());

    $sql = 'insert into form_exemplo.acesso_user(
        login_user
       ,pwd_user
       ,sit_ativo
       ,idpessoa
       ) values (:val0,:val1,:val2,null)';
    $stmt = $conn->prepare($sql);  
    $stmt->bindValue(':val0', 'login'.rand(1, 3)); 
    $stmt->bindValue(':val1', null); 
    $stmt->bindValue(':val2', 'S'); 
    $result = $stmt->execute();
    var_dump($result);

    $res1 = $conn->commit();    
    var_dump($res1);
} catch (Exception $e) {
  $conn->rollBack();
  echo "Failed: " . $e->getMessage();
}