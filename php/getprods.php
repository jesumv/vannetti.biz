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
/*** obtiene proveedor si lo hay ***/
if(isset($_GET['idprov'])){$cad = "idproveedores=".$_GET['idprov']; }else{$cad = "1"; }

/**trae datos de productos activos**/
    if (is_object($mysqli)) {
    	$sqlCommand = "SELECT idproductos,nom_corto,costo,iva,ieps FROM productos WHERE ".$cad."
         AND status = 0 ORDER BY nom_corto";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE SELEC PROD. ".mysqli_error($mysqli));
//inicializacion de arreglo
			 while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('id' => $tempo['idproductos'],
			 	    'nombre' => $tempo['nom_corto'],'costo'=>$tempo['costo'],
			 	    'iva'=>$tempo['iva'],'ieps'=>$tempo['ieps']
			 	);
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