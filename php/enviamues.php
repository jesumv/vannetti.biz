<?php
function myAutoload($ClassName)
{
    require('include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

	
    $funcbase = new dbutils;
	
		function movmuestra($mysqli,$prod,$monto,$fecha,$cant,$usu,$coment='NULL'){
				$ref="muestra";
				$sfactu=1;
				$mysqli->autocommit(false);
			try{
		//movimientos contables en diario.
				for ($i=0; $i <2 ; $i++) {
						if($i==0){$cuenta="602.61";$column="debe";}else{$cuenta="115.01";$column="haber";}
						$mysqli->query("INSERT INTO diario(cuenta,referencia,$column,fecha,facturar,coment)
						VALUES($cuenta,'$ref',$monto,'$fecha',$sfactu,'$coment')");
						//efectuar la operacion
						$mysqli->commit();
					}
		//salida de inventario
					$cadena="MUESTRA PARA ".$coment;
					$mysqli->query("INSERT INTO inventario (idproductos,tipomov,cant,fechamov,usu,comentarios,factu,haber)
					VALUES($prod,2,$cant,'$fecha','$usu','$cadena',1,$monto)");
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
		$jsondata = array();
		//recolección de variables
		$usu= $_SESSION['usuario'];
		$prod=$_POST['prod'];
		$fecha=$_POST['fecha'];
		$cant=$_POST['cant'];
		$costo=$_POST['costo'];
		$recep=$_POST['recep'];
		//afectacion a bd
		$resul=movmuestra($mysqli,$prod,$costo,$fecha,$cant,$usu,$recep);	
		mysqli_close($mysqli);	
	}else{$resul=-1;} 
    $jsondata['resul'] =$resul;
	echo json_encode($jsondata);