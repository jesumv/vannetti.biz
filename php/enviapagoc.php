<?php
   /*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
	
	//funciones auxiliares
	require '../include/funciones.php';
    $funcbase = new dbutils;
	$resul;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
    	/**inicializa variable resultado**/
    	$resul=0;
    	/*** checa login***/
    	$funcbase->checalogin($mysqli);
		//creacion de arreglo de resultado
		$jsondata = array();
		//recoleccion de variables
		$sfact= $_POST["sfact"];
		$refer= $_POST["oc"];
		$idprov=$_POST["idprov"];
		$fecha=$_POST["fecha"];
		$factura=$_POST["factu"];
		$arch=$_POST["arch"];
		$metpago=$_POST["metpago"];
		//prevision para cuando no hay cuenta
		$cta;
		if (!$_POST["cta"]){$cta=NULL;}else{$cta=$_POST["cta"];};
		$folio=$_POST["folio"];
		$subt= $_POST["subt"];
		$iva= $_POST["iva"];
		$total= $_POST["total"];	
		//afectacion a bd
			$resul=epagoc($mysqli, $fecha,$refer,$subt,$iva,$total,$factura,$metpago,$sfact,$idprov,$folio,$arch,$cta);
			mysqli_close($mysqli);
	}else{
				$resul=-1;
			}
//salida
		$jsondata['resul'] = $resul;
   		echo json_encode($jsondata);	