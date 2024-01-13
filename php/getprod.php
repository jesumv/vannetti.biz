<?php
/*** Esta rutina obtiene datos de productos para mostrar en tablas ***/
function myAutoload($ClassName)
{
    require('include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
/*** obtiene producto seleccionado si lo hay ***/
$idprod = $_GET['idprod'];
$cad = "idproductos=".$idprod;
/**trae datos del/los producto(s) solicitado**/
    if (is_object($mysqli)) {
    	$sqlCommand = "SELECT idproveedores,grupo,nombre,nom_corto,nom_cat,codigo,unidad,cant,cbarras,
    	costo,costov,descripcion,iva,speso,precio1,precio2,precio3,precio4,spesov,ieps FROM productos WHERE ".$cad.
    	" ORDER BY idproductos";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE PRODUCTO ".mysqli_error($mysqli));
//inicializacion de arreglo
			 while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('idprod'=> $idprod,'idprov' => $tempo['idproveedores'],'grupo' => $tempo['grupo'],
			 	'nombre' => $tempo['nombre'],'nomcorto' => $tempo['nom_corto'],'nomcat' => $tempo['nom_cat'],
			 	'codigo'=>$tempo['codigo'],'unidad'=>$tempo['unidad'],'cant'=>$tempo['cant'],
			 	'cbarras'=>$tempo['cbarras'],'costo'=>$tempo['costo'],'costov'=>$tempo['costov'],'desc'=>$tempo['descripcion'],
			 	'iva'=>$tempo['iva'],'speso'=>$tempo['speso'],'pr1'=>$tempo['precio1'],'pr2'=>$tempo['precio2'],
			 	    'pr3'=>$tempo['precio3'],'pr4'=>$tempo['precio4'],'spesov'=>$tempo['spesov']);
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