<?php
/*** Esta rutina obtiene la lista de ordenes pendientes***/
	/*** Autoload class files ***/ 
function myAutoload($ClassName)
{
    require('include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
/*** obtiene proveedor si lo hay ***/
if(isset($_GET['idprov'])){$cad = "idproveedores=".$_GET['idprov']; }else{$cad = "1"; }

/**trae datos de ordenes de compra sin surtir**/
    if (is_object($mysqli)) {
    	$sqlCommand = "SELECT idoc, arts FROM oc WHERE ".$cad." AND status < 10 ORDER BY idoc";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE OC SOL. ".mysqli_error($mysqli));
//inicializacion de arreglo
			$filas = $query1->num_rows;
			if($filas > 0){
				while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('id' => $tempo['idoc'],'arts' => $tempo['arts']);
			 }
			}else{$result=$filas;};
			 
	/* liberar la serie de resultados */
			  mysqli_free_result($query1);			  
	/* cerrar la conexi�n */
	  mysqli_close($mysqli);
	  
	 echo json_encode($result);
	 
	}else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
?>