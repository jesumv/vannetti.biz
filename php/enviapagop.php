<?php
   /*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
	
	//funciones auxiliares
	require '../include/funciones.php';
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
    	/**inicializa variable resultado**/
    	/*** checa login***/
    	$funcbase->checalogin($mysqli);
		//creacion de arreglo de resultado
		$jsondata = array();
		//recoleccion de variables
		$sfact= $_POST["sfact"];
		$refer= $_POST["pedido"];
		$fechap= $_POST["fecha"];
		$factura=$_POST["factu"];
		$subt= $_POST["subt"];
		$iva= $_POST["iva"];
		$total= $_POST["total"];
		$metpago=$_POST["metpago"];
		$idcte=$_POST["idcte"];
		$arch=$_POST["arch"];
		//afectacion a bd
			$resul=epagoped($mysqli, $fechap, $refer, $subt, $iva,$total,$factura,$metpago,$sfact,$idcte,$arch);
			mysqli_close($mysqli);
	}else{
				$resul=-1;
			}
//salida
		$jsondata['resul'] = $resul;
   		echo json_encode($jsondata);	