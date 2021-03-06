<?php

function datosppago($total,$tipopago,$fechamov){
    //define variables segun tipo de pago.
    $resulp=array();
        //status arts siempre surtidos
     $resulp['statusa']=99;
    switch ($tipopago){
        case 0:
            //efectivo
            $resulp['fpago']=$fechamov;
            $resulp['tipovta']=0;
            $resulp['status']=40;
            $resulp['saldo']=0;
            $resulp['tpago']=0;
            break;
        case 1:
            //t credito
            $resulp['fpago']=$fechamov;;
            $resulp['tipovta']=1;
            $resulp['status']=40;
            $resulp['saldo']= 0;
            $resulp['tpago']=1;
            break;
            
        case 2:
            //transferencia
            $resulp['fpago']=$fechamov;
            $resulp['tipovta']=1;
            $resulp['status']=40;
            $resulp['saldo']= 0;
            $resulp['tpago']=2;
            break;
        case 3:
            //x cobrar
            //tipo de pago todavia no conocido
            $resulp['fpago']=null;
            $resulp['tipovta']=2;
            $resulp['status']=30;
            $resulp['saldo']=$total;
            $resulp['tpago']=99;
    }
    return $resulp;
}

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

function operdiario($mysqli,$cuenta,$tipoper,$tipom,$ref,$monto,$fecha,$sfactu,$subcta=NULL,$coment=NULL){
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
		$mysqli->query("INSERT INTO diario(cuenta,referencia,$colum,fecha,facturar,subcuenta,coment)
        VALUES($cuenta,'$refe',$monto,'$fecha',$sfactu,'$subcta','$coment')")
		or die("Error en registro contable: ".mysqli_error($mysqli));
}

function metpago($metpago){
	//define cuenta de abono segun metodo pago de pedidos
	if($metpago=="01"){$cuenta="101.01";}else{$cuenta="102.01";}
	return $cuenta;
}
function epagoped($mysqli,$fecha,$ref,$monto,$iva,$total,$saldoi,$factu,$mpago,$sfactu,$cte,$saldof,$arch=NULL){
	//registra un pago de pedido a credito
	//define los montos de pago
    $pagoiva =  defiva($monto,$saldoi,$monto);
    //definicion del status de pago
    if($monto<$saldoi){$status = 35;}else{$status = 40;};
			try{
				$mysqli->autocommit(false);
			//la referencia es pedido
				$tipoper=1;
			//movimientos de diario
				//cargo en entrada de efectivo segun metodo de pago
					$cuenta1=metpago($mpago);
					$tipom1=0;
					operdiario($mysqli, $cuenta1, $tipoper, $tipom1, $ref, $monto, $fecha,$sfactu);
			//abono a clientes
					$cuenta4="105.01";
					$tipom4=1;
					operdiario($mysqli,$cuenta4,$tipoper,$tipom4,$ref,$monto,$fecha,$sfactu,$cte);
				//cargo a iva trasladado no cobrado
					$cuenta2="209.01";
					$tipom2=0;
					operdiario($mysqli,$cuenta2,$tipoper,$tipom2,$ref,$iva,$fecha,$sfactu);
				//abono a iva trasladado
					$cuenta3="208.01";
					$tipom3=1;
					operdiario($mysqli,$cuenta3,$tipoper,$tipom3,$ref,$iva,$fecha,$sfactu);
			//actualizacion en pedidos
					if($arch!=NULL){
					    $mysqli->query("UPDATE pedidos SET status=$status,fechapago='$fecha',
                        factura='$factu',saldo=$saldof,arch='$arch' WHERE idpedidos='$ref'");}else {
                        $mysqli->query("UPDATE pedidos SET status=$status,fechapago='$fecha',
                        factura='$factu',saldo=$saldof WHERE idpedidos='$ref'");
					    }
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


function defiva($subt,$saldoi,$pago){
    //define el monto a aplicar a iva
    $pagoiva;
   $resto = $saldoi -$subt;
   if($resto >0){
       // queda iva por aplicar
       if($resto>$pago){$pagoiva=$pago;}else{$pagoiva=$resto;}
   }else{
       //todo a capital
       $pagoiva = 0;
   }
   return $pagoiva;
}

function epagoc($mysqli,$fecha,$ref,$monto,$iva,$total,$saldoi,$factu,$mpago,$sfactu,$prov,$folio,$montop,
    $saldof,$cta,$arch=NULL,$comi=NULL,$civa=NULL){
	//registra pago de una orden de compra
	//define los montos de pago
	$pagoiva =  defiva($monto,$saldoi,$montop);
	//definicion del status de pago
	if($montop<$saldoi){$status = 90;}else{$status = 99;};
			try{
				$mysqli->autocommit(false);
			//la referencia es oc
				$tipoper=0;
			//movimientos de diario
				//cargo en salida de efectivo de recursos segun metodo de pago
					$cuenta1=metpago($mpago);
					//tipo 0 es debe
					$tipom1=1;
					operdiario($mysqli,$cuenta1,$tipoper,$tipom1,$ref,$montop,$fecha,$sfactu,$cta);
				//abono a proveedores
					$cuenta2="201.01";
					$tipom2=0;
					operdiario($mysqli,$cuenta2,$tipoper,$tipom2,$ref,$montop,$fecha,$sfactu,$prov);
				//abono a iva acreditable por pagar
					$cuenta3="119.01";
					$tipom3=1;
					operdiario($mysqli,$cuenta3,$tipoper,$tipom3,$ref,$pagoiva,$fecha,$sfactu);
				//cargo a iva acreditable pagado
					$cuenta4="118.01";
					$tipom4=0;
					operdiario($mysqli,$cuenta4,$tipoper,$tipom4,$ref,$pagoiva,$fecha,$sfactu);
				//movimientos de comisiones, si las hay, siempre se factura
					if($comi!=NULL){
					    //cargo a bancos
					    $cuentacomi="102.01";
					    $tipomc=1;
					    $comitot = $comi+$civa;
					    operdiario($mysqli,$cuentacomi,$tipoper,$tipomc,$ref,$comitot,$fecha,1,NULL,'comisiones banc');
					    //abono a gastos financieros e iva pagado
					    operdiario($mysqli,"701.10",$tipoper,0,$ref,$comi,$fecha,1,NULL,'comisiones banc');
					    operdiario($mysqli,"118.01",$tipoper,0,$ref,$civa,$fecha,1,NULL,'comisiones banc');					    
					}
				//actualizacion de orden de compra
				$archm;
				if($arch!=NULL){
					$archm= substr($arch,11);
				}else{$archm = NULL;}
					$mysqli->query("UPDATE oc SET status=$status,fechapago='$fecha',saldo=$saldof,factura='$factu',arch='$archm',foliomov='$folio' WHERE idoc='$ref'");				
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

function vtdc($venta){
    //define montos para asientos contables venta tdc
    $resulv=array();
    $resulv['comis']=$venta*0.035;
    $resulv['ivaxpag']=$resulv['comis']*0.16;
    return $resulv;
}

function venta($mysqli,$fecha,$ref,$monto16,$monto0,$iva,$tipo,$factu,$cte,$ieps=0){
		//esta funcion inserta en el diario los movimientos de una venta
		//en todos los casos
		$tmonto=$monto16+$monto0;
		$montof=$tmonto+$iva+$ieps;
		//inicializa variable resultados
			//calculo del costo de ventas
		    $costoc=$mysqli->query("SELECT SUM(haber)FROM 
            inventario WHERE tipomov = 2 AND idoc= $ref");
		    if(!$costoc){$jsondata['errorsql']= mysqli_error($mysqli);
		    $costoc->close();
		    throw new Exception("error en consulta costov",11);
		    }
			$row=mysqli_fetch_row($costoc);
			$costo=$row[0];
			$costoc->close();
			//si existe el costo de ventas?
			if($costo!=''){
					//abono a inventario 115.01
					$abono="115.01";
					operdiario($mysqli,$abono,1,1,$ref,$costo,$fecha,$factu);
					//cargo a costo de ventas 501.01
					$cargo="501.01";
					operdiario($mysqli,$cargo,1,0,$ref,$costo,$fecha,$factu);
					//abono a ventas segun tasa
					//ventas tasa general 
					if($monto16>0){
					    $abono="401.01";
					    operdiario($mysqli,$abono,1,1,$ref,$monto16,$fecha,$factu,$cte);
					}					
					//ventas tasa 0
					if($monto0>0){
					    $abono="401.04";
					    operdiario($mysqli,$abono,1,1,$ref,$monto0,$fecha,$factu,$cte);
					}
					//movimientos por tipo de venta
						switch($tipo){
							case 0:
								//mostrador- cargo a caja y efectivo 101.01
								$cargo="101.01";
								operdiario($mysqli,$cargo,1,0,$ref,$montof,$fecha,$factu);
								//iva trasladado cobrado 208.01
								$abono="208.01";
								operdiario($mysqli,$abono,1,1,$ref,$iva,$fecha,$factu);
								//ieps trasladado cobrado si lo hay
								if($ieps>0){
								    $abono="208.02";
								    operdiario($mysqli,$abono,1,1,$ref,$ieps,$fecha,$factu);
								}
								
								break;
							case 1:
							    $ctdc=vtdc($montof);
								//tdc –– cargo a cxc corto
								$cargo="106.01";
								operdiario($mysqli,$cargo,1,0,$ref,$montof,$fecha,1,1);								
								//iva trasladado cobrado 208.01
								$abono="208.01";
								operdiario($mysqli,$abono,1,1,$ref,$iva,$fecha,$factu);
								//ieps trasladado cobrado
								if($ieps>0){
								    $abono="208.02";
								    operdiario($mysqli,$abono,1,1,$ref,$ieps,$fecha,$factu);
								}
								break;
							case 2:
								//transferencia
								$cargo="102.01";
								operdiario($mysqli,$cargo,1,0,$ref,$montof,$fecha,$factu);
								//iva trasladado cobrado 
								$abono="208.01";
								operdiario($mysqli,$abono,1,1,$ref,$iva,$fecha,$factu);
								//ieps trasladado cobrado
								if($ieps>0){
								    $abono="208.02";
								    operdiario($mysqli,$abono,1,1,$ref,$ieps,$fecha,$factu);
								}
								break;
							case 3:
							    //x cobrar
							    $cargo="105.01";
							    operdiario($mysqli,$cargo,1,0,$ref,$montof,$fecha,$factu,$cte);
							    //iva trasladado no cobrado 209.01
							    $abono="209.01";
							    operdiario($mysqli,$abono,1,1,$ref,$iva,$fecha,$factu);
							    //ieps trasladado no cobrado
							    if($ieps>0){
							        $abono="209.02";
							        operdiario($mysqli,$abono,1,1,$ref,$ieps,$fecha,$factu);
							    }
							    break;
							default:
							    throw new Exception("error: mov no definido",44);
						}
					
				    $resul=0;
				
			}else{
				//no hay costo de ventas
			    throw new Exception("error: no hay costo de ventas",33);
			}
		
		 return $resul;
}
	
