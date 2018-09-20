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
		$saldoi=$_POST["saldoi"];
		$montop= $_POST["montop"];
		$saldof= $_POST["saldof"];
		$metpago=$_POST["metpago"];
		$idcte=$_POST["idcte"];
		$arch=$_POST["arch"];
		$saldof=$saldoi-$montop;
		//afectacion a bd
			$resul=epagoped($mysqli, $fechap, $refer,$montop,$iva,$subt,$saldoi,$factura,$metpago,$sfact,$idcte,$saldof,$arch);
			mysqli_close($mysqli);
	}else{
				$resul=-1;
			}
//salida
		$jsondata['resul'] = $resul;
		$jsondata['saldof'] = $saldof;
   		echo json_encode($jsondata);	