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
$idprod = $_GET['idprod'];
$cad = "idproductos=".$idprod;
/**trae datos del producto solicitado**/
    if (is_object($mysqli)) {
    	$sqlCommand = "SELECT idproveedores,grupo,nombre,nom_corto,codigo, unidad,cant, cbarras,
    	costo,descripcion, iva, precio1, precio2, precio3 FROM productos WHERE ".$cad;		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE PRODUCTO ".mysqli_error($mysqli));
//inicializacion de arreglo
			 while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('idprod'=> $idprod,'idprov' => $tempo['idproveedores'],'grupo' => $tempo['grupo'],
			 	'nombre' => $tempo['nombre'],'nomcorto' => $tempo['nom_corto'],
			 	'codigo'=>$tempo['codigo'],'unidad'=>$tempo['unidad'],'cant'=>$tempo['cant'],
			 	'cbarras'=>$tempo['cbarras'],'costo'=>$tempo['costo'],
			 	'desc'=>$tempo['descripcion'],'iva'=>$tempo['iva'],'pr1'=>$tempo['precio1'],'pr2'=>$tempo['precio2'],
				'pr3'=>$tempo['precio3']);
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