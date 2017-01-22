<?php

function diasvenc($fechamov,$diascred){
		//esta funcion calcula los dias vencidos de una factura
		$a=ceil((time() - (strtotime($fechamov)))/(60* 60*24))-$diascred;
		//if($a>0){$amod=$a;}else{$amod=0;}
		$amod = ($a < 0 ? 0 : $a);
		return $amod;
	}

function cambiafact($booleano){
	//esta funcion cambia booleano por entero
		switch($booleano){
			case "true":
			$resul= 1;
			break;
			case "false":
			$resul= 0;
			break;
		}
		return $resul;
}
function converfecha($fechao){
	//esta funcion convierte una fecha dd/MM/AAA en aaaa-mm-dd
	$cuenta = 0;
	$año=substr($fechao,-4,4);
	$mes=substr($fechao,-7,2);
	$dia=substr($fechao,0,2);
	//checa si la fecha recibida es valida
		if(is_numeric($año)&&is_numeric($mes)&&is_numeric($dia)){$fechac=$año."-".$mes."-".$dia;}else{$fechac=$fechao;}
	return $fechac;
}

function operdiario($mysqli,$cuenta,$tipoper,$tipom,$ref,$monto,$fecha,$sfactu,$subcta='NULL'){
		//esta funcion realiza 1 movimiento contable en diario. $tipom determina
		//si el movimiento es debe = 0 o haber = else
		//determinacion de referencia orden de compra o pedido
		if($tipoper==0){$refe="oc".$ref;}else{$refe="pd".$ref;}
		//determinacion de tipo de movimiento
		if($tipom==0){
			$colum="debe";
		}else{
			$colum="haber";
		}
		$mysqli->query("INSERT INTO diario(cuenta,referencia,$colum,fecha,facturar,subcuenta)VALUES($cuenta,'$refe',$monto,'$fecha',$sfactu,$subcta)");
}

function metpago($metpago){
	//define cuenta de abono segun metodo pago de pedidos
	if($metpago=="01"){$cuenta="101.01";}else{$cuenta="102.01";}
	return $cuenta;
}
function epagoped($mysqli,$fecha,$ref,$monto,$iva,$total,$factu,$mpago,$sfactu,$cte,$arch=NULL){
	//registra un pago de pedido a credito
			try{
				$mysqli->autocommit(false);
			//la referencia es pedido
				$tipoper=1;
			//movimientos de diario
				//cargo en entrada de efectivo segun metodo de pago
					$cuenta1=metpago($mpago);
					$tipom1=0;
					operdiario($mysqli, $cuenta1, $tipoper, $tipom1, $ref, $total, $fecha,$sfactu);
			//abono a clientes
					$cuenta4="105.01";
					$tipom4=1;
					operdiario($mysqli,$cuenta4,$tipoper,$tipom4,$ref,$total,$fecha,$sfactu,$cte);
				//cargo a iva trasladado no cobrado
					$cuenta2="209.01";
					$tipom2=0;
					operdiario($mysqli,$cuenta2,$tipoper,$tipom2,$ref,$iva,$fecha,$sfactu);
				//abono a iva trasladado
					$cuenta3="208.01";
					$tipom3=1;
					operdiario($mysqli,$cuenta3,$tipoper,$tipom3,$ref,$iva,$fecha,$sfactu);
			//actualizacion en pedidos
					$mysqli->query("UPDATE pedidos SET status=40,fechapago='$fecha',factura='$factu',arch='$arch' WHERE idpedidos='$ref'");
			//efectuar la operacion
				$mysqli->commit();
				$resul=0;
			}catch (Exception $e) {
					//error en las operaciones de bd
				    $mysqli->rollback();
				   	$resul=-2;
				}
	return $resul;
}

function epagoc($mysqli,$fecha,$ref,$monto,$iva,$total,$factu,$mpago,$sfactu,$prov,$folio,$arch=NULL,$cta=NULL){
	//registra pago de una orden de compra
			try{
				$mysqli->autocommit(false);
			//la referencia es oc
				$tipoper=0;
			//movimientos de diario
				//cargo en salida de efectivo de efectivo segun metodo de pago
					$cuenta1=metpago($mpago);
					//tipo 0 es debe
					$tipom1=1;
					operdiario($mysqli,$cuenta1,$tipoper,$tipom1,$ref,$total,$fecha,$sfactu,$cta);
				//abono a proveedores
					$cuenta2="201.01";
					$tipom2=0;
					operdiario($mysqli,$cuenta2,$tipoper,$tipom2,$ref,$monto,$fecha,$sfactu,$prov);
				//abono a iva acreditable por pagar
					$cuenta3="119.01";
					$tipom3=1;
					operdiario($mysqli,$cuenta3,$tipoper,$tipom3,$ref,$iva,$fecha,$sfactu);
				//cargo a iva acreditable pagado
					$cuenta4="118.01";
					$tipom4=0;
					operdiario($mysqli,$cuenta4,$tipoper,$tipom4,$ref,$iva,$fecha,$sfactu);
				//actualizacion de orden de compra
					$mysqli->query("UPDATE oc SET status=99,fechapago='$fecha',factura='$factu',arch='$arch',foliomov='$folio' WHERE idoc='$ref'");				
			//efectuar la operacion
				$mysqli->commit();
				$resul=0;
			}catch (Exception $e) {
					//error en las operaciones de bd
				    $mysqli->rollback();
				   	$resul=-2;
				}
	return $resul;
}	
function venta($mysqli,$fecha,$ref,$monto16,$monto0,$iva,$tipo,$factu,$cte){
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
					//ventas tasa general 
						$abono="401.01";
						operdiario($mysqli,$abono,1,1,$ref,$monto16,$fecha,$factu,$cte);
					//ventas tasa 0 
						$abono="401.04";
						operdiario($mysqli,$abono,1,1,$ref,$monto0,$fecha,$factu,$cte);
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
								//credito cargo a clientes
								$cargo="105.01";
								operdiario($mysqli,$cargo,1,0,$ref,$montof,$fecha,$factu,$cte);
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
		
		 return $resul;
}
	
