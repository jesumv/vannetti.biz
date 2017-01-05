<?php
/*** Autoload class files ***/ 
    function __autoload($class){
      require('/include/' . strtolower($class) . '.class.php');
    }
	function operdiario($mysqli,$cuenta,$tipoper,$tipom,$ref,$monto,$fecha,$factu){
	//esta funcion realiza 1 movimiento contable en diario. $tipom determina
	//si el movimiento es debe o haber
	//determinacion de referencia
	if($tipoper==0){$refe="oc".$ref;}else{$refe="pd".$ref;}
	//determinacion de tipo de movimiento
	if($tipom==0){
		$colum="debe";
	}else{
		$colum="haber";
	}
	$mysqli->query("INSERT INTO diario(cuenta,referencia,$colum,fecha,facturar)VALUES($cuenta,'$refe',$monto,'$fecha',$factu )");
	
		
	}	
		function venta($mysqli,$fecha,$ref,$monto16,$monto0,$iva,$tipo,$factu){
		//esta funcion inserta en el diario los movimientos de una venta
		//en todos los casos
		$table='diario';
		$tmonto=$monto16+$monto0;
		$montof=$tmonto+$iva;
		//inicializa variable resultados
			//calculo del costo de ventas
		    $costoc=$mysqli->query("SELECT SUM(haber)FROM inventario WHERE tipomov = 2 AND idoc= $ref");
			$row=mysqli_fetch_row($costoc);
			$costo=$row[0];
			$costoc->close();	
			//si existe el costo de ventas?
			if($costo!=''){
				try {
					$mysqli->autocommit(false);
					//abono a inventario 115.01
					$abono="115.01";
					operdiario($mysqli,$abono,1,1,$ref,$costo,$fecha,$factu);
					//cargo a costo de ventas 501.01
					$cargo="501.01";
					operdiario($mysqli,$cargo,1,0,$ref,$costo,$fecha,$factu);
					//abono a ventas segun tasa
					//ventas tasa general 401.01
						$abono="401.01";
						operdiario($mysqli,$abono,1,1,$ref,$monto16,$fecha,$factu);
					//ventas tasa 0 401.04
						$abono="401.04";
						operdiario($mysqli,$abono,1,1,$ref,$monto0,$fecha,$factu);
					//movimientos por tipo de venta
						switch($tipo){
							case 0:
								//mostrador- cargo a caja y efectivo 101.01
								$cargo="101.01";
								operdiario($mysqli,$cargo,1,0,$ref,$montof,$fecha,$factu);
								//iva trasladado cobrado 208.01
								$abono="208.01";
								operdiario($mysqli,$abono,1,1,$ref,$iva,$fecha,$factu);
								break;
							case 1:
								//contado x ahora igual a anterior, luego se manda al cobro
								//mostrador- cargo a caja y efectivo 101.01
								$cargo="101.01";
								operdiario($mysqli,$cargo,1,0,$ref,$montof,$fecha,$factu);
								//iva trasladado cobrado 208.01
								$abono="208.01";
								operdiario($mysqli,$abono,1,1,$ref,$iva,$fecha,$factu);
								break;
							case 2:
								//credito cargo a clientes 105.01
								$cargo="105.01";
								operdiario($mysqli,$cargo,1,0,$ref,$montof,$fecha,$factu);
								//iva trasladado no cobrado 209.01
								$abono="209.01";
								operdiario($mysqli,$abono,1,1,$ref,$iva,$fecha,$factu);	
								break;
								
						}
					
					//efectuar la operacion
					$mysqli->commit();
				    $resul=0;
				} catch (Exception $e) {
					//error en las operaciones de bd
				    $mysqli->rollback();
				   	$resul=-2;
				}
				
			}else{
				//no hay costo de ventas
				$resul=-1;
			}
		
		//return$resul;
		echo "resultado: ".$resul;
	}
	
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
    	venta($mysqli,"2017-01-05","1",60.00,40.00,16.00,2,1);
    }else{echo "ERROR EN CONEXION";}


?>