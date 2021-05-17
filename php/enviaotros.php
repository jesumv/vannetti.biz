<?php
 /*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    //funciones auxiliares
    require_once '../include/fauxgasto.php';
    
    $funcbase = new dbutils;
	$resul;
	
function movtras($mysqli,$origen,$destino,$monto,$fecha,$ref){
    //registra movimientos de traspasos entre cuentas
    
        $mysqli->autocommit(false);
        if(!$mysqli->query("INSERT INTO diario(cuenta,referencia,haber,fecha,facturar)
			VALUES($origen,'$ref',$monto,'$fecha',1)"))throw new Exception(" EN REG TRANSP HABER: $mysqli->error");
        IF(!$mysqli->query("INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)
			VALUES($destino,'$ref',$monto,'$fecha',1)"))throw new Exception(" EN REG TRANSP DEBE: $mysqli->error");;
        //efectuar la operacion
        $mysqli->commit();
        $resul=0;
    return $resul;
    
}
	function movivag($mysqli,$ref,$monto,$fecha,$concep){
		//registra movimientos en iva acreditable o por pagar, dependiendo del tipo de operacion
		//tipo es el metodo de pago elegido
			$cuenta="118.01";
			$resuli=0;
			if (!$mysqli->query("INSERT INTO diario(cuenta,referencia,debe,fecha,facturar,coment)
			VALUES($cuenta,'$ref',$monto,'$fecha',1,'$concep')")){
			throw new Exception(" EN REG IVA: $mysqli->error");
			return $resuli; 
			}
	}
	
	
	function montos($catgasto,$st,$iva){
	    //determina si el gasto es todo el monto o sin iva, para no deducibles
	    //para el caso cuando se carga el gasto con factura.
	    switch ($catgasto){
	        case "601.83":
	        case "602.83":
	        case "603.81":
	            $s=$st+$iva;
	            $i=0;
	            break;
	        default:
	            $s=$st;
	            $i=$iva;
	    }
	    $arraym["st"]=$s;
	    $arraym["iv"]=$i;
	    return $arraym;
	}
	
	function ccargo($metpago){
	    //define la cuenta a afectar segun metodo de pago
	    //seleccionado
	    $abono;
	    switch($metpago){
	        //efectivo
	        case "01":
	            $abono="101.01";
	            break;
	            //cheque
	        case "02":
	            //transferencia
	        case "03":
	            //tarjeta de debito
	        case "13":
	            //cargo a cuenta
	        case "28":
	            $abono="102.01";
	            break;
	            //tarjetas de credito
	        case "04":
	            $abono="205.06";
	            break;
	            //otros
	        default:
	            $abono="205.06";
	    }
	    return $abono;
	    
	}
/*** conexion a bd ***/
	$resul;
	$mal=Null;
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
		//correcion cuando no hay iva
		if(empty($_POST["iva"])){$iva=0;}else{$iva=$_POST["iva"];};
		//numero de factura
		$fact=$_POST["fact"];
		$arch=$_POST["arch"];
		$catg=$_POST["catg"];
		$concep=$_POST["concep"];
		$metpago=$_POST["metpago"];
		$monto=$_POST["monto"];
		$cuenta=$_POST["cuenta"];
		$folio=$_POST["folio"];
		$pordeduc=$_POST["pordeduc"];
		$mprop=$_POST["mprop"];
		$efec=$_POST["efec"];
		$ivaaux=$_POST["ivaaux"];
		$origen=$_POST["orig"];
		$destino=$_POST["dest"];
		if(empty($_POST["uuid"])){$uuid="";}else{$uuid=$_POST["uuid"];};
		$total=$monto+$iva;
		$resul1=0;
		$resul2=0;
		$resul3=0;

		//afectacion a bd
		switch($tipo){
			case "g":
			//definicion de montos si el monto se carga con factura
			    if($iva!=0){
			        $montos= montos($catg,$monto,$iva);
			        $subt = $montos['st'];
			        $ivac = $montos['iv'];
			    }else{
			        $subt=$monto;
			        $ivac = $iva;
			    }
			    
			    //conversion de metodo de pago
			    $ccargo =  ccargo($metpago);
			    // $tipo 0 se traduce a debe, 1 a haber
			    //debe a gastos
			    try{
			        $resul1=movdiario($mysqli,$catg,"",0,$subt,$fecha,$concep,NULL,$arch);
			        //iva
			        $resul3=movivag($mysqli,$fact,$ivac,$fecha,$concep);
			        //haber a cuenta origen del pago
			        $resul2=movdiario($mysqli,$ccargo,"",1,$total,$fecha,$concep,$cuenta,$arch);
			        $resul=$resul1+$resul2+$resul3;
			        //anota movimientos auxiliares si los hay
			        if($metpago=="03" || $mprop!=""){movaux($mysqli,$metpago,$ccargo,$fact,$concep,
			            $mprop,$efec,$ivaaux,$fecha,$cuenta,$arch);}
			            mysqli_close($mysqli);
			    }catch(Exception $e){
			        $mal=$e->getMessage();
			        $resul=-2;
			    }

        	break;	
			default:
			    try{
			        $resul=movtras($mysqli, $origen, $destino, $monto, $fecha, $concep);
			    }catch(Exception $e){
			        $mal=$e->getMessage();
			        $resul=-3;
			    }
			
		}
	}else{
	    $mal=$mysqli->connect_error;
	    $resul=-1;
	} 
	$jsondata['resul'] =$resul;
	$jsondata['mal'] =$mal;
	echo json_encode($jsondata); 