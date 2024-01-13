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
/**trae id y no corto de proveedores no cancelados**/
    if (is_object($mysqli)) {
    	$sqlCommand = "SELECT idproveedores,nom_corto FROM proveedores  WHERE status < 2 ORDER BY nom_corto";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE SELEC PROV. ".mysqli_error($mysqli));
//inicializacion de arreglo
			 while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('id' => $tempo['idproveedores'],'nombre' => $tempo['nom_corto']);
			 };

	/* liberar la serie de resultados */
			  mysqli_free_result($query1);			  
	/* cerrar la conexi�n */
	  mysqli_close($mysqli);
	  
	 echo json_encode($result);
	 
	}else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
?>