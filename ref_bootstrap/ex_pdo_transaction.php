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

function debug($var){
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function showSelect($conn,$sql){
    $stmt = $conn->query($sql, PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll(); 
    debug($result[0]['qtd']);
}

$conn->beginTransaction();       
try { 
    //getAllAttributes($conn);
    
    $sql = "select count(*) as qtd from form_exemplo.acesso_user";
    showSelect($conn,$sql);

    $sql = "select count(*) as qtd from form_exemplo.acesso_perfil_user";
    showSelect($conn,$sql);

    var_dump($conn->inTransaction());
    $login = 'login'.rand(1, 100);

    $sql = 'insert into form_exemplo.acesso_user(
        login_user
       ,pwd_user
       ,sit_ativo
       ,idpessoa
       ) values (:val0,:val1,:val2,null)';
    $stmt = $conn->prepare($sql);  
    $stmt->bindValue(':val0', $login); 
    $stmt->bindValue(':val1', null); 
    $stmt->bindValue(':val2', 'S'); 
    $result = $stmt->execute();

    $sql = "select * from form_exemplo.acesso_user
            where login_user = '".$login."'";
    $stmt = $conn->query($sql, PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll();    
    //var_dump($result);

    $iduser = $result[0]['iduser'];

    $sql = 'insert into form_exemplo.acesso_perfil_user(
            idperfil
            ,iduser
            ,sit_ativo
            ) values (4,:val0,:val1)';
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':val0', $iduser); 
    $stmt->bindValue(':val1', 'S');
    $result = $stmt->execute();


    $sql = "select count(*) as qtd from form_exemplo.acesso_user";
    showSelect($conn,$sql);

    $sql = "select count(*) as qtd from form_exemplo.acesso_perfil_user";
    showSelect($conn,$sql);

    throw new Exception('xxx');

    $sql = 'delete form_exemplo.acesso_perfil_user
            where iduser = :val0';
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':val0', $iduser); 
    $result = $stmt->execute();

    $sql = 'delete form_exemplo.acesso_user
            where login_user = :val0';
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':val0', $login); 
    $result = $stmt->execute();


    $res1 = $conn->commit();    
    var_dump($res1);
} catch (Exception $e) {
  $conn->rollBack();
  echo "Failed: " . $e->getMessage();
}