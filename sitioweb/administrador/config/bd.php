<?php
    $host="localhost";
    $bd="sitio";
    $usuario="root";
    $constrasenia="";

    try {
        
        $conexion= new PDO("mysql: host=$host; dbname=$bd", $usuario, $constrasenia);
        
        if($conexion){ 
            
        }

    } catch (Exception $ex) {
        
        echo $ex->getMessage();
    }
    ?>
