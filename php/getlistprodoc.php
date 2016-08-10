<?php
/*** Esta rutina obtiene los productos de una orden de compra***/
	/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
/*** obtiene proveedor si lo hay ***/
if(!isset($_GET['oc'])){$result= 0 ;}else{
	/**trae datos de ordenes de compra sin surtir**/
    if (is_object($mysqli)) {
    	$oc= $_GET['oc'];
    	$sqlCommand = "SELECT t1.idartsoc, t1.cant, t2.nom_corto FROM artsoc AS t1 LEFT JOIN productos AS t2
    	ON t1.idproductos = t2.idproductos WHERE idoc = ".$oc." AND t1.status = 1 ORDER BY t1.idartsoc";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE ARTSOC SOL. ".mysqli_error($mysqli));
//inicializacion de arreglo
			$filas = $query1->num_rows;
			if($filas > 0){
				while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('idart' => $tempo['idartsoc'],'cant' => $tempo['cant'],'nom' => $tempo['nom_corto']);
			 }
			}else{$result= 1;}
			 
	/* liberar la serie de resultados */
			  mysqli_free_result($query1);	
	}else{die ("<h1>'No se establecio la conexion a bd'</h1>");};
    		  
	/* cerrar la conexi�n */
}
	 mysqli_close($mysqli);
	 echo json_encode($result);	 	
?>