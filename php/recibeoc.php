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
	
	function traeproov($mysqli,$oc){
		$sql = "SELECT idproveedores FROM oc WHERE idoc=".$oc;
		$query = mysqli_query($mysqli, $sql) or die ('error en consulta no. proov: '.mysqli_error($mysqli));
		$row=mysqli_fetch_array($query);
		$proov = $row[0];
		return $proov;
	}
	
	function facturar($mysqli,$oc){
		//esta funcion informa si el proveedor factura para  calcular el iva
		$sql = "SELECT t1.idproveedores, t2.factura FROM oc AS t1 INNER JOIN proveedores as t2
		ON t1.idproveedores = t2.idproveedores WHERE t1.idoc = $oc";
		$query = mysqli_query($mysqli, $sql) or die ('error en consulta idproov: '.mysqli_error($mysqli));
		$row=mysqli_fetch_array($query);
		$fact= $row[1];
		return $fact;
	
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
	$fact= facturar($mysqli,$oc);
	$ivat= array();
	$totalt=array();
	//revisar que la oc no esté ya ingresada
		$revisa = revisastoc($oc, $mysqli);
		$jsondata['strevisa']= $revisa;
		if($revisa==0){
			//trae no proveedor
				$proov=traeproov($mysqli, $oc);
				foreach ($arts as $valor) {
				//marcar los articulos como ingresados
		   			$sqlCommand = "UPDATE artsoc SET status= 2 WHERE idartsoc=".$valor;
					$query1 = mysqli_query($mysqli, $sqlCommand) or die ('error en marcado de arts recep '.mysqli_error($mysqli));		
			//abono a inventario
					$sqlCommand1 = "INSERT INTO inventario (idproductos,tipomov,cant,monto,usu,idoc,debe)
		    		SELECT idproductos, $tipomov, cant,preciot,'$usu',$oc,preciot from artsoc WHERE idartsoc=".$valor; 
					$query2 = mysqli_query($mysqli, $sqlCommand1) or die ('error en alta inventarios: '.mysqli_error($mysqli));
			//detectar si causa iva
					$sqlCommand4a = "SELECT t2.idproductos,t1.costo FROM productos AS t1 INNER JOIN artsoc AS t2 ON 
					t1.idproductos = t2.idproductos WHERE t2.idartsoc=$valor";
					$query4a= mysqli_query($mysqli, $sqlCommand4a) or die ('error en busc iva: '.mysqli_error($mysqli));
					$row=mysqli_fetch_array($query4a);
					$prod= $row[0];
					$cost= $row[1];
					if($fact==1){$ivaprod = $funcbase->iva($mysqli,$prod,$cost);
					array_push($ivat,$ivaprod);
					}else{$ivaprod=0;}
					$movtotot=$cost+$ivaprod;
					array_push($totalt,$movtotot);
			//cargo a cxp
					$sqlCommand4 = "INSERT INTO cxp (idmovto,idproveedores,monto,iva,total,usu,haber)
		    		SELECT artsoc.idoc,oc.idproveedores,$cost,$ivaprod,$movtotot,
		    		'$usu',$movtotot FROM  artsoc INNER JOIN oc ON artsoc.idoc=oc.idoc where artsoc.idartsoc =".$valor; 
					$query4 = mysqli_query($mysqli, $sqlCommand4) or die ('error en cargo a cxp: '.mysqli_error($mysqli));		
			//cargo a ivaacred
					$sqlCommand5 = "INSERT INTO ivaacred(idmovto,idproveedores,usu,status,debe)
					VALUES($oc,$proov,'$usu',1,$ivaprod)";
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
			//marcar orden de compra como ingresada	y actualizar iva de los productos recibidos
			$ivaft=array_sum($ivat);
			$totalft=array_sum($totalt);
			$sqlCommand3 = "UPDATE oc SET status =".$tsurt." ,iva = $ivaft, total = $totalft WHERE idoc = $oc";
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
