<?php
 /*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
	
    $funcbase = new dbutils;
	$resul;
	
	function movdiario($mysqli,$cuenta,$tipom,$ref,$monto,$fecha,$sfactu,$subcta='NULL'){
		//esta funcion realiza 1 movimiento contable en diario. $tipom determina
		//determinacion de tipo de movimiento
		if($tipom==0){
			$colum="debe";
		}else{
			$colum="haber";
		}
		try{
			$mysqli->autocommit(false);
			$mysqli->query("INSERT INTO diario(cuenta,referencia,$colum,fecha,facturar,subcuenta)
			VALUES($cuenta,'$ref',$monto,'$fecha',$sfactu,'$subcta')");
			//efectuar la operacion
			$mysqli->commit();
			$resul=0;
		}catch(Exception $e){
			//error en las operaciones de bd
			$mysqli->rollback();
			$resul=-2;
		}
		return $resul;
}
	
	function moviva($mysqli,$tipo,$ref,$monto,$fecha,$sfactu){
		//registra movimientos en iva acreditable o por pagar, dependiendo del tipo de operacion
		//tipo es el metodo de pago elegido
		$cuenta;
		switch ($tipo) {
			case 4:
				//por pagar
				$cuenta="119.01";
				break;
			
			default:
				//acreditable
				$cuenta="118.01";
		}
		
		try{
			$mysqli->autocommit(false);
			$mysqli->query("INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)
			VALUES($cuenta,'$ref',$monto,'$fecha',$sfactu)");
			//efectuar la operacion
			$mysqli->commit();
			$resul=0;
		}catch(Exception $e){
			//error en las operaciones de bd
			$mysqli->rollback();
			$resul=-3;
		}
		return $resul;
		
	}
	
		function movtras($mysqli,$origen,$destino,$monto,$fecha,$ref){
		//registra movimientos de traspasos entre cuentas
		
		try{
			$mysqli->autocommit(false);
			$mysqli->query("INSERT INTO diario(cuenta,referencia,haber,fecha,facturar)
			VALUES($origen,'$ref',$monto,'$fecha',1)");
			$mysqli->query("INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)
			VALUES($destino,'$ref',$monto,'$fecha',1)");
			//efectuar la operacion
			$mysqli->commit();
			$resul=0;
		}catch(Exception $e){
			//error en las operaciones de bd
			$mysqli->rollback();
			$resul=-3;
		}
		return $resul;
		
	}
	
	function cgasto($tipo,$mpago){
		$cargo;
		$abono;
		switch($tipo){
			//gastos generales
			case 1:
				$cargo="601";
			break;
			//gastos de venta
			case 2:
				$cargo="602";
			break;
			//gastos de administracion
			case 3:
				$cargo="603";
			break;
			//gastos financieros
			case 4:
				$cargo="701";
			break;
			//reembolso de gastos
			case 5:
				$cargo="205";
			break;
			//otros gastos
			default:
				$cargo="703";
		}
			switch($mpago){
			//efectivo
				case 1:
					$abono="101.01";
				break;
			//cheque
				case 2:
			//transferencia
				case 3:
					$abono="102.01";
				break;	
			//tarjetas de credito
				case 4:
					$abono="205.01";
				break;	
			//otros
				default:
				$abono="101.01";	
			}
		$array["c"]=$cargo;
		$array["a"]=$abono;
		return $array;
	}
	
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if(is_object($mysqli)){
		/*** checa login***/
    	$funcbase->checalogin($mysqli);
		//creacion de arreglo de resultados
		$jsondata = array();
		//recolección de variables	
		$tipo= $_POST["tipo"];
		$fecha=$_POST["fecha"];
		$monto=$_POST["monto"];
		$iva=$_POST["iva"];
		$fact=$_POST["fact"];
		$arch=$_POST["arch"];
		$catg=$_POST["catg"];
		$concep=$_POST["concep"];
		$mpago=$_POST["mpago"];
		$cuenta=$_POST["cuenta"];
		$folio=$_POST["folio"];
		$origen=$_POST["orig"];
		$destino=$_POST["dest"];
		$total=$monto+$iva;
		$resul1=0;
		$resul2=0;
		$resul3=0;
		//determinacion de facturacion
		if($fact){$facturar=1;}else{$facturar=0;}
		//afectacion a bd
		switch($tipo){
			case "g":
        		//definicion de cuentas
				$cuentas=cgasto($catg,$mpago);
				$cargo=$cuentas['c'];
				$abono=$cuentas['a'];
				//cargo a gastos
				$resul1=movdiario($mysqli,$cargo,0,$concep,$monto,$fecha,$facturar);
				//abono a cuenta origen del pago
				$resul2=movdiario($mysqli,$abono,1,$concep,$total,$fecha,$facturar,$cuenta);
				//iva
				$resul3=moviva($mysqli,$mpago,$concep,$iva,$fecha,$facturar);
        	break;	
			default:
				$resul1=movtras($mysqli, $origen, $destino, $monto, $fecha, $concep);
				
		}
		$resul=$resul1+$resul2+$resul3;
		mysqli_close($mysqli);
	}else{$resul=-1;} 
	$jsondata['resul'] =$resul;
	echo json_encode($jsondata); 