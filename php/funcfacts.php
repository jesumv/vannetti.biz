<?php
/*** Autoload class files ***/
function myAutoload($ClassName)
{
    require('include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

$funcbase = new dbutils;
$resul=0;
$mal=0;
//creacion de arreglo de resultados
$jsondata = array();
/*** funciones individuales ***/
function buscauuid($mysqli,$uuid){
    //busca si una factura ya está en bd
    $sqlCommand = "SELECT UUID FROM factrec WHERE UUID=".$uuid;
    // Execute the query here now
    try {
        $query1=mysqli_query($mysqli, $sqlCommand);
    } catch(Exception $e) {
        $mal= "ERROR EN CONSULTA UUID".mysqli_error($mysqli);
    } 
}

function checarecep($rfc){
    if($rfc==="MAVJ621021AQA"){return 0;}else{return -2;};
}

function checafact($mysqli,$rfc,$uuid) {
    //valida si una factura de gasto
    $resulc=0;
    //está lista para ser incluida
    $resulc=$resulc+checarecep($rfc);
    return $resulc;
}

/*** conexion a bd ***/
$mysqli = $funcbase->conecta();
if(is_object($mysqli)){
    /*** checa login***/
    $funcbase->checalogin($mysqli);
    $resul=checafact($mysqli,"MAVJ621021AQA","");
    if($resul===0){
        
    }
}else{
    $mal=$mysqli->connect_error;
    $resul=-1;
}
/*** cierra la conexion***/
mysqli_close($mysqli);
$jsondata['resul'] =$resul;
$jsondata['mal'] =$mal;
echo json_encode($jsondata); 
