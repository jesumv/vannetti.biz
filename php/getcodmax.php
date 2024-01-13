<?php
/*** Esta rutina obtiene datos de productos para mostrar en tablas ***/
	/*** Autoload class files ***/ 
function myAutoload($ClassName)
{
    require('../include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
/**trae datos del producto solicitado**/
    if (is_object($mysqli)) {
    	$sqlCommand = "SELECT MAX(idproductos) FROM productos";	
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA IDPRODUCTOMAX ".mysqli_error($mysqli));
//obtencion del dato
			 $maxidprod=mysqli_fetch_row($query1);
			 $result = $maxidprod;
	/* liberar la serie de resultados */
			  mysqli_free_result($query1);			  
	/* cerrar la conexi�n */
	  mysqli_close($mysqli);
	  
	 echo json_encode($result);
	 
	}else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
?>