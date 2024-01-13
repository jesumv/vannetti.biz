<?php
/*** Esta rutina obtiene todos los productos para mostrar en autocomplete***/
/*** y otros datos necesarios de la orden de compra***/
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
if(!isset($_GET['oc'])){$result= 0 ;}else{
	/**trae datos de ordenes de compra sin surtir**/
    if (is_object($mysqli)) {
    	$oc= $_GET['oc'];
    	//obtener datos de la orden de compra
    	//obtener datos de los articulos no surtidos
    	$sqlCommand = "SELECT t1.idartsoc, t1.cant, t2.nom_corto, t2.speso, t2.costo,t2.iva,t2.ieps,t3.nombre AS ud FROM artsoc AS t1 LEFT JOIN productos AS t2
    	ON t1.idproductos = t2.idproductos INNER JOIN  unidades AS t3 ON t2.unidad= t3.idunidades WHERE t1.idoc = ".$oc." AND t1.status = 1 ORDER BY t2.nom_corto";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE ARTSOC SOL. ".mysqli_error($mysqli));
	//trae las unidades para la caja de dialogo
//inicializacion de arreglo
			$filas = $query1->num_rows;
			if($filas > 0){
				while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('idart' => $tempo['idartsoc'],'cant' => $tempo['cant'],'nom' => $tempo['nom_corto'],
			 	    'speso' => $tempo['speso'],'costo' => $tempo['costo'],'civa'=>$tempo['iva'],'cieps'=>$tempo['ieps'],'ud'=>$tempo['ud']);
			 }
			}else{$result= 1;}
			 
	/* liberar la serie de resultados */
			  mysqli_free_result($query1);	
	}else{die ("<h1>'No se establecio la conexion a bd'</h1>");};
    		  
	/* cerrar la conexi�n */
}
	 mysqli_close($mysqli);
	 echo json_encode($result);	 	
