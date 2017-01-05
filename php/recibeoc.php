<?php
/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
	
	//funciones auxiliares
	require '../include/funciones.php';
	
	function compra($mysqli,$monto,$refe,$tipo,$prov,$iva,$fact,$fechaconv ){
		//esta funcion registra en el diario una compra a credito
			$cargo="115.01";
			$total = $monto+$iva;
		switch($tipo){
			//contado
			case 0:
				    $cargo2="118.01";
					$cabono="101.01";
				//cargo
					$sqlmov ="INSERT INTO diario(cuenta,referencia,debe,fecha)VALUES($cargo,'$refe',$monto,'$fechaconv' )";
					$querydiac = mysqli_query($mysqli, $sqlmov) or die ('error en cargo1: '.mysqli_error($mysqli));
					$sqlmov ="INSERT INTO diario(cuenta,referencia,debe,fecha)VALUES($cargo2,'$refe',$iva,'$fechaconv' )";
					$querydiac = mysqli_query($mysqli, $sqlmov) or die ('error en cargo2: '.mysqli_error($mysqli));			
				//abono
					$sqlmov ="INSERT INTO diario(cuenta,referencia,haber,fecha)VALUES($cabono,'$refe',$total,'$fechaconv' )";
					$querydiac = mysqli_query($mysqli, $sqlmov) or die ('error en abono: '.mysqli_error($mysqli));			
			break;
			//credito
			default:
					$cargo2="119.01";
					$cabono="201.01";
				//cargo
					$sqlmov ="INSERT INTO diario(cuenta,referencia,debe,fecha)VALUES($cargo,'$refe',$monto,'$fechaconv' )";
					$querydiac = mysqli_query($mysqli, $sqlmov) or die ('error en cargo1: '.mysqli_error($mysqli));
					$sqlmov ="INSERT INTO diario(cuenta,referencia,debe,fecha)VALUES($cargo2,'$refe',$iva,'$fechaconv')";
					$querydiac = mysqli_query($mysqli, $sqlmov) or die ('error en cargo2: '.mysqli_error($mysqli));	
				//abono
					$sqlmov ="INSERT INTO diario(cuenta,subcuenta,referencia,haber,fecha)VALUES($cabono,$prov,'$refe',$total,'$fechaconv' )";
					$querydiac = mysqli_query($mysqli, $sqlmov) or die ('error en abono: '.mysqli_error($mysqli));
			break;
		}
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
	
	function traedatosoc($mysqli,$oc){
		$datos = [];
		//esta funcion trae los datos utiles de la oc
		$sql = "SELECT idproveedores,facturar, credito FROM oc WHERE idoc=".$oc;
		$query = mysqli_query($mysqli, $sql) or die ('error en consulta no. proov: '.mysqli_error($mysqli));
		$row=mysqli_fetch_array($query);
		$datos[0] = $row[0];
		$datos[1] = $row[1];
		$datos[2] = $row[2];
		return $datos;
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
	$remi = $_POST["remi"];
	$fact =$_POST["fact"];
	$montot = $_POST["monto"];
	$ivat = $_POST["ivat"];
	$fechar= $_POST["fechar"];
	$usu= $_SESSION['usuario'];
	$tipomov = 1;
	$fechaconv = converfecha($fechar);
	//revisar que la oc no esté ya ingresada
		$revisa = revisastoc($oc, $mysqli);
		if($revisa==0){
		//si la oc no esta ingresada, se procede al registro
		//trae no proveedor
				$datos[]=traedatosoc($mysqli, $oc);
				$prov= $datos[0][0];
				$facturar = $datos[0][1];
				$credito=$datos[0][2];
				$refe="oc".$oc;
				
//ciclo de afectacion a bd
		//registro en diario
			compra($mysqli,$montot,$refe,$credito,$prov,$ivat,$facturar,$fechaconv);
			$cantarts = count($arts);
			$jsondata['resul'] = 0;
			for($i=0;$i<$cantarts;$i++) {
				$id= $arts[$i][0];
				$cant= $arts[$i][1];
				$costo= $arts[$i][2];
				
				//cargo a inventario
						$sqlCommand1 = "INSERT INTO inventario (idproductos,tipomov,cant,monto,usu,idoc,debe,factu,fechamov)
			    		SELECT idproductos,$tipomov,$cant,$costo,'$usu',$oc,$costo,$facturar,'$fechaconv' from artsoc WHERE idartsoc=".$id; 
						$query1 = mysqli_query($mysqli, $sqlCommand1) or die ('error en alta inventarios: '.mysqli_error($mysqli));
						if($query1){
							//marcar los articulos como ingresados a inventario
				   			$sqlCommand = "UPDATE artsoc SET cant= $cant,status= 2 WHERE idartsoc=".$id;
							$query2 = mysqli_query($mysqli, $sqlCommand) or die ('error en marcado de arts recep '.mysqli_error($mysqli));
							if(!$query2){$jsondata['resul'] = -1;};
						}else{$jsondata['resul'] = -1;}
					
			}
		
		//determinar si se surte total o parcial
			$tsurt = tiposurt($mysqli,$oc);
		//actualizacion de oc	
			$grant=$montot+$ivat;
			$sqlCommand3 = "UPDATE oc SET status =".$tsurt." ,iva = $ivat, monto=$montot, total = $grant, factura= '$fact', 
			remision='$remi',fecharec='$fechaconv' WHERE idoc = $oc";
			$query3 = mysqli_query($mysqli, $sqlCommand3) or die ('error en marcado de oc rec '.mysqli_error($mysqli));  
		/*complementar el array de resultados*/ 
			$jsondata['noc']= $oc; 
			$jsondata['tipos']= $tsurt; 
			
		}else{
			//de otro modo se regresa error.
			$jsondata['resul'] = $revisa;
		}
	
    }else{
    		$jsondata['resul'] = -90;
    }
    
    /* cerrar la conexion */
	    mysqli_close($mysqli);
	//salida de respuesta
		 echo json_encode($jsondata);
