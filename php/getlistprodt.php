<?php
/*** Esta rutina obtiene todos los productos para mostrar en autocomplete***/
	/*** Autoload class files ***/ 
function myAutoload($ClassName)
{
    require('../include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
	/**trae datos de ordenes de compra sin surtir**/
    if (is_object($mysqli)) {
		$cadena=$_GET["q"];
    	$sqlCommand = "SELECT idproductos,nom_corto,costov  FROM  productos  WHERE nom_corto LIKE ('%".$cadena."%') ORDER BY nom_corto";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE LISTA PRODUCTOS. ".mysqli_error($mysqli));
	//trae los productos para la caja de dialogo
//inicializacion de arreglo
			$filas = $query1->num_rows;
			if($filas > 0){
				while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('idprod' => $tempo['idproductos'],'nombrc' => $tempo['nom_corto'],'costo' => $tempo['costov']);
			 }
			}else{$result= 1;}
			 
	/* liberar la serie de resultados */
			  mysqli_free_result($query1);	
	}else{die ("<h1>'No se establecio la conexion a bd'</h1>");};
    		  
	/* cerrar la conexi�n */

	 mysqli_close($mysqli);
	 echo json_encode($result);	 	
?>