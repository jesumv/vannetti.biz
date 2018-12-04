<?php
//envia numero de factura a bd
function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
	
    $funcbase = new dbutils;
    
	function movfact($mysqli,$idpedidos,$nofact){
	    $resul;
				$mysqli->autocommit(false);
			try{$mysqli->query("UPDATE pedidos SET factura = '$nofact', status=30 WHERE idpedidos = $idpedidos");
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
	/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if(is_object($mysqli)){
		/*** checa login***/
    	$funcbase->checalogin($mysqli);
		//creacion de arreglo de resultados
		$jsondata;
		//recolección de variables
		$content = trim(file_get_contents("php://input"));
		$json = json_decode($content,true);
		$pedido=$json["pedido"];
		$factura=$json["factura"];
		//afectacion a bd
		$resul=movfact($mysqli,$pedido,$factura);	
		mysqli_close($mysqli);	
	}else{$resul=-1;} 
    $jsondata['resul'] =$resul;
	echo json_encode($jsondata);