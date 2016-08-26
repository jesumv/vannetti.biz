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
		$resul = 2;
	}elseif ($compa === 0) {
		//surtido total
		$resul = 3;
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
		if($stoc<3){
			return 0;
		}else{
		    return -1;
		}
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
	//revisar que la oc no esté ya ingresada
		$revisa = revisastoc($oc, $mysqli);
		$jsondata['strevisa']= $revisa;
		if($revisa==0){
			//marcar los articulos como ingresados
				foreach ($arts as $valor) {
		   			$sqlCommand = "UPDATE artsoc SET status= 2 WHERE idartsoc=".$valor;
					$query1 = mysqli_query($mysqli, $sqlCommand) or die ('error en marcado de arts recep '.mysqli_error($mysqli));
			//abono a inventario
					$sqlCommand1 = "INSERT INTO inventario (idproductos,tipomov,cant,monto,usu,idoc,debe)
		    		SELECT idproductos, $tipomov, cant,preciot,'$usu',$oc,preciot from artsoc WHERE idartsoc=".$valor; 
					$query2 = mysqli_query($mysqli, $sqlCommand1) or die ('error en alta inventarios: '.mysqli_error($mysqli));
			//cargo a cxp
					$sqlCommand4 = "INSERT INTO cxp (idmovto,idproveedores,monto,iva,total,usu,haber)
		    		SELECT artsoc.idoc,oc.idproveedores,artsoc.preciot,0, preciot,
		    		'$usu',preciot FROM  artsoc INNER JOIN oc ON artsoc.idoc=oc.idoc where artsoc.idartsoc =".$valor; 
					$query4 = mysqli_query($mysqli, $sqlCommand4) or die ('error en cargo a cxp: '.mysqli_error($mysqli));
			//cargo a ivaacred
					$sqlCommand5 = "INSERT INTO ivaacred (idmovto,monto,usu,haber)
		    		SELECT idoc,0,'$usu',0 FROM artsoc WHERE idartsoc =".$valor; 
					$query5 = mysqli_query($mysqli, $sqlCommand5) or die ('error en cargo iva acred: '.mysqli_error($mysqli));
				}
			//validacion de ingreso
				if(!$query1||!$query2){$jsondata['resul'] = -1;};
				if(!$query4){$jsondata['resul'] = -2;};
			//determinar si se surte total o parcial
			$tsurt = tiposurt($mysqli,$oc);
			/*complementar el array de resultados*/ 
			$jsondata['noc']= $oc; 
			$jsondata['tipos'] = $tsurt;
			
			
			//marcar orden de compra como ingresada	
			$sqlCommand3 = "UPDATE oc SET status =".$tsurt." WHERE idoc = $oc";
				$query3 = mysqli_query($mysqli, $sqlCommand3) or die ('error en marcado de oc rec '.mysqli_error($mysqli));  
			//validacion de ingreso
			if(!$query3){$jsondata['resul'] = -3;};
			//validacion de alta mov
			if(!$query4||!$query5){$jsondata['resul'] = -2;};	
		}else{
			$jsondata['resul'] = -99;
		}
	
	/* cerrar la conexion */
	    mysqli_close($mysqli);
	//salida de respuesta
		 echo json_encode($jsondata);
    }else{
    	
    }
