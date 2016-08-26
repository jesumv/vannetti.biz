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
	//creacion de pedido en tabla pedido
	$sqlCommand= "INSERT INTO pedidos (idclientes,arts,monto,total,usu,status)
	    	VALUES ($cte,$totarts,$total,$total,'$usu',$status)";
	    	$query=mysqli_query($mysqli, $sqlCommand)or die("error en alta oc:".mysqli_error($mysqli)); 
			//obtencion de numero de orden de pedido
			$nped = traepedmax($mysqli);
			if($query){
				//se inicia asumiendo que el resultado es ok.
				$jsondata['success'] = 0;
	//---------------CICLO POR CADA PRODUCTO DEL PEDIDO
	//insercion de productos en tabla artspedidos
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
								//registro de movimientos segun tipo de venta
								if($tipoventa==0){
									//inventarios		
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
									//caja y bancos
									$sqlCommand= "INSERT INTO disponible (idmovto,idclientes,cuenta,usu,debe)
									VALUES ($nped,$cte,1,'$usu',$total)";
									$query=mysqli_query($mysqli, $sqlCommand)or die("error en abono caja: ".mysqli_error($mysqli));
									if(!$query){
										$jsondata['success'] = -4;	
									}	
									//ingresos por ventas
									$sqlCommand= "INSERT INTO ingresos (idmovto,idclientes,usu,haber)
									VALUES ($nped,$cte,'$usu',$total)";
									$query=mysqli_query($mysqli, $sqlCommand)or die("error en abono ingresos: ".mysqli_error($mysqli));
									if(!$query){
										$jsondata['success'] = -5;	
									}
									//costo de ventas
									$sqlCommand= "INSERT INTO cvtas (idmovto,idclientes,usu,debe)
									VALUES ($nped,$cte,'$usu',$cvtas)";
									$query=mysqli_query($mysqli, $sqlCommand)or die("error en abono cvtas: ".mysqli_error($mysqli));
									if(!$query){
										$jsondata['success'] = -5;	
									}
									
									//iva por pagar
								}
								
								
					}else{$jsondata['success'] = -1;}
						
			/* cerrar la conexion */
	    	mysqli_close($mysqli);  

	//salida de respuesta
		 echo json_encode($jsondata);
    }else{
    	
    }
