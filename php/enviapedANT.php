<?php
/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
	
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
    	/*** checa login***/
    	$funcbase->checalogin($mysqli);
		
	
	function traepedmax($mysqli){
		$req = "SELECT MAX(idpedidos) FROM pedidos WHERE 1"; 
    	$result = mysqli_query($mysqli,$req);
		$row=mysqli_fetch_array($result,MYSQLI_NUM);
	 /* liberar la serie de resultados */
	    mysqli_free_result($result);
		return $row[0];
	}
	
	function traecv($mysqli,$prod){
		$req = "SELECT costo FROM productos WHERE idproductos = $prod "; 
    	$result = mysqli_query($mysqli,$req);
		$row=mysqli_fetch_array($result,MYSQLI_NUM);
	 /* liberar la serie de resultados */
	    mysqli_free_result($result);
		return $row[0];
	}
	
    //recoleccion de variables
    $cte = $_POST["cte"];
	$totarts=$_POST["totarts"];
	$tipoventa= $_POST["tipoventa"];
	$facturar= $_POST["facturarp"];
	$prods=$_POST["prods"];
	$cants=$_POST["cants"];
	$preciou = $_POST["preciou"];
	$preciot = $_POST["preciot"];
	$total=$_POST["total"];
	$jsondata = array();
	//creacion de datos para el pedido
	$usu= $_SESSION['usuario'];
	$iva=0;
	//definicion de status segun tipo de compra
	if($tipoventa ==0){$status = 99;}else{$status = 1;}
	if($facturar==true){$ivaped=$total*.16;}else{$ivaped=0;}
	//1 creacion de pedido en tabla pedido
	$sqlCommand= "INSERT INTO pedidos (idclientes,arts,monto,iva,total,usu,status)
	    	VALUES ($cte,$totarts,$total,$ivaped,$total+$ivaped,'$usu',$status)";
	    	$query=mysqli_query($mysqli, $sqlCommand)or die("error en alta pedidos:".mysqli_error($mysqli)); 
			//obtencion de numero de orden de pedido
			$nped = traepedmax($mysqli);
			if($query){
				$ivaped;
				$iva =array();
				//se inicia asumiendo que el resultado es ok.
				$jsondata['success'] = 0;
	//---------------CICLO POR CADA PRODUCTO DEL PEDIDO
	//2 insercion de productos en tabla artspedidos
				$indi = 0;
	//inicializacion de costo de ventas
				$cvtas= 0;;
						foreach($prods as $id){
							//obtencion del costo de materia prima
							$cvtaprod= traecv($mysqli, $id);
							$sqlCommand= "INSERT INTO artsped (idpedido,idproductos,cant,preciou,
							preciot,status)
			    			VALUES ($nped,$id,$cants[$indi],$preciou[$indi],$preciot[$indi],$status)";
							$query=mysqli_query($mysqli, $sqlCommand)or die("error en alta artsped:".mysqli_error($mysqli)); 	
							if($query){
								//incremento del cv del pedido
								$cvtas = $cvtas+$cvtaprod;
								//incremento del iva de la orden
								$ivaprod = $funcbase->iva($mysqli,$id,$preciot[$indi]);
								$iva[]=$ivaprod;
								//registro de movimientos segun tipo de venta
								if($tipoventa==0){
									//3 inventarios	si es pedido de contado	
									$sqlCommand= "INSERT INTO inventario (idproductos,tipomov,cant,usu,idoc,monto,haber)
									VALUES ($id,2,-$cants[$indi],'$usu',$nped,$cvtaprod,$cvtaprod)";
									$query=mysqli_query($mysqli, $sqlCommand)or die("error en salida invent: ".mysqli_error($mysqli));
									if(!$query){
										$jsondata['success'] = -3;	
									}												
								}

							
							}else{$jsondata['success'] = -2;}
								$indi++;
							};
//---FIN DEL CICLO POR ARTICULO------------------
								//variables de regreso segun resultado
								$jsondata['nped'] = $nped;
								$jsondata['arts'] = $totarts;
								$jsondata['total'] =$total;
								//si el resultado de las anteriores operaciones es ok, se continua con los movimientos contables
								if($jsondata['success'] == 0){
									//si es venta contado
									if($tipoventa==0){
										//4 caja y bancos
										$sqlCommand= "INSERT INTO disponible (idmovto,idclientes,cuenta,usu,debe)
										VALUES ($nped,$cte,1,'$usu',$total)";
										$query=mysqli_query($mysqli, $sqlCommand)or die("error en abono caja: ".mysqli_error($mysqli));
										if(!$query){
											$jsondata['success'] = -4;	
										}
										//4a ingresos por ventas
										$sqlCommand= "INSERT INTO ingresos (idmovto,idclientes,usu,haber)
										VALUES ($nped,$cte,'$usu',$total)";
										$query=mysqli_query($mysqli, $sqlCommand)or die("error en abono ingresos: ".mysqli_error($mysqli));
										if(!$query){
											$jsondata['success'] = -5;	
										}
										//4b costo de ventas
										$sqlCommand= "INSERT INTO cvtas (idmovto,idclientes,usu,debe)
										VALUES ($nped,$cte,'$usu',$cvtas)";
										$query=mysqli_query($mysqli, $sqlCommand)or die("error en abono cvtas: ".mysqli_error($mysqli));
										if(!$query){
											$jsondata['success'] = -5;	
										}
									//si es a credito, solo se registra el pedido.
									}	
									
									//si es contado, y facturable, se registra el iva
									//6 iva por pagar
									
									if($facturar== 'true'){$ivacalc= array_sum($iva);}else{$ivacalc=0;}
									
									$sqlCommand= "INSERT INTO ivatrans (idmovto,idclientes,usu,status,haber)
										VALUES ($nped,$cte,'$usu',0,$ivacalc)";
										$query=mysqli_query($mysqli, $sqlCommand)or die("error en abono ivatrans: ".mysqli_error($mysqli));
										if(!$query){
											$jsondata['success'] = -6;	
										}
								}
								
								
					}else{$jsondata['success'] = -1;}
						
			/* cerrar la conexion */
	    	mysqli_close($mysqli);  

	//salida de respuesta
		 echo json_encode($jsondata);
    }else{
    	
    }
