<?php

/*** Autoload class files ***/ 
function myAutoload($ClassName)
{
    require('../include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
/**trae id y nom_corto de clientes no cancelados**/
    if (is_object($mysqli)) {
        header("Content-Type: application/json; charset=UTF-8");
        $obj = json_decode($_REQUEST['dat'],true); 
        $rfc=$obj['rfc'];
        $sqlCommand = "SELECT idclientes,nom_corto FROM clientes  WHERE rfc ='$rfc'";
        //Execute the query here now
        $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE SELEC CTES. "
            .mysqli_error($mysqli));
        //process result
        if($query1){
            $tempolong= mysqli_num_rows($query1);
            $tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC);
            $result;
            if($tempolong!=0){
                $result=array('exito' => 0,'idctes' => $tempo['idclientes'],'nomcorto' => $tempo['nom_corto']);
            }else{
                $result=array('exito'=>1);
            }
        }else{$result=array('exito'=>-1);}
        
        /* liberar la serie de resultados */
        mysqli_free_result($query1);
        /* cerrar la conexi?n */
        mysqli_close($mysqli);
        
        echo json_encode($result);
        
    }