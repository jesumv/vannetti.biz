<?php
/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
	
	
function tiposurt($mysqli,$oc){
	//esta funcion examina si una oc ha sido totalmente surtida
	//el numero de arts en la oc
	$sql="SELECT arts FROM oc WHERE idoc =".$oc;
	$result=mysqli_query($mysqli,$sql);
	$row=mysqli_fetch_array($result);
	$arts = $row[0];
	//el numero de arts surtidos
	$sql="SELECT SUM(cant) FROM artsoc WHERE idoc =".$oc." AND status = 2";
	$result=mysqli_query($mysqli,$sql);
	$row=mysqli_fetch_array($result);
	$sarts = $row[0];
	// compara ambos
	$compa = $arts-$sarts;
	
	if($compa === $arts){
		//no se ha surtido nada
		$resul = 0;
	}elseif ($compa>0) {
		//surtido parcial
		$resul = 10;
	}elseif ($compa === 0) {
		//surtido total
		$resul = 11;
	}else{
		//error
		$resul = -99;
	}
	//resultado
	return $resul;
}	
	
	function revisastoc($oc,$mysqli){
	//esta funcion revisa que una orden de compra no esté ingresada previamente
	//para no duplicar inventarios
		$sqlrevisa = "SELECT status FROM oc WHERE idoc=".$oc;
	$queryrev = mysqli_query($mysqli, $sqlrevisa) or die ('error en consulta st oc: '.mysqli_error($mysqli));
	$row=mysqli_fetch_array($queryrev);
	$stoc = $row[0];
		if($stoc<11){
			return 0;
		}else{
		    return -99;
		}
	}
	
	function traeproov($mysqli,$oc){
		//esta funcion trae el numero del proveedor de la orden de compra
		$sql = "SELECT idproveedores FROM oc WHERE idoc=".$oc;
		$query = mysqli_query($mysqli, $sql) or die ('error en consulta no. proov: '.mysqli_error($mysqli));
		$row=mysqli_fetch_array($query);
		$proov = $row[0];
		return $proov;
	}
	
	
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
    	/*** checa login***/
    	$funcbase->checalogin($mysqli);
	//inicializacion de variable de resultados
	$jsondata = array();
    //recoleccion de variables
    $oc = $_POST["oc"];
	$arts= $_POST["arts"];
	$usu= $_SESSION['usuario'];
	$tipomov = 1;
	$ivat= array();
	$totalt=array();
	//revisar que la oc no esté ya ingresada
		$revisa = revisastoc($oc, $mysqli);
		$jsondata['strevisa']= $revisa;
	//si la oc no esta ingresada, se procede al registro
		if($revisa==0){}else{}
	/* cerrar la conexion */
	    mysqli_close($mysqli);
	//salida de respuesta
		 echo json_encode($jsondata);
    }else{
    	
    }
