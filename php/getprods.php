<?php
/*** Esta rutina obtiene datos de productos para mostrar en tablas ***/
	/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
/*** obtiene proveedor si lo hay ***/
if(isset($_GET['idprov'])){$cad = "idproveedores=".$_GET['idprov']; }else{$cad = "1"; }

/**trae id y nom corto de productos no cancelados**/
    if (is_object($mysqli)) {
    	$sqlCommand = "SELECT idproductos,nom_corto,costo FROM productos WHERE ".$cad." ORDER BY nom_corto";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE SELEC PROD. ".mysqli_error($mysqli));
//inicializacion de arreglo
			 while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('id' => $tempo['idproductos'],'nombre' => $tempo['nom_corto'],'costo'=>$tempo['costo']);
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