<?php
/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
	
	//funciones auxiliares
	require '../include/funciones.php';

	function traepedmax($mysqli){
		//traer el np. de pedido mas alto
		$req = "SELECT MAX(idpedidos) FROM pedidos WHERE 1"; 
    	$result = mysqli_query($mysqli,$req);
		$row=mysqli_fetch_array($result,MYSQLI_NUM);
		$cuenta=count($row);
		if(is_null($row)){$pedmax=0;}else{$pedmax=$row[0];}
	 /* liberar la serie de resultados */
	    mysqli_free_result($result);
		return $pedmax;
	}
	
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    //creacion de datos para la venta de mostrador
    $jsondata = array();
    
    if (is_object($mysqli)) {
        try{
            /*** checa login***/
            $funcbase->checalogin($mysqli);
            /**inicializa variable resultado**/
            $resul=0;
            //para las ventas por tasa
            $vtas16= array();
            $vtas0=array();
            //recoleccion de variables
            $cte=$_POST["cte"];
            $fecha= $_POST["fecha"];
            $cte= $_POST["cte"];
            $fechaconv = converfecha($fecha);
            $tpago=$_POST['tipopago'];
            //cambiar variable a booleano
            $totarts=$_POST["totarts"];
            $montot= $_POST["montot"];
            $totiva=$_POST["totiva"];
            $total=$_POST["total"];
            $usu= $_SESSION['usuario'];
            $most= $_SESSION['mostrador'];
            //arreglo con datos de producto
            $arts=$_POST['prods'];
            //definicion de variables
            $rpago= datosppago($total,$tpago,$fechaconv);         
            $fpago=$rpago['fpago'];
            $tventa=$rpago['tipovta'];
            $status=$rpago['status'];
            $saldo=$rpago['saldo'];
            $tpagom= $rpago['tpago'];
            //afectacion a bd
            $table="pedidos";
            //insercion  pedido
            $mysqli->autocommit(false);
            $query=$mysqli->query("INSERT INTO $table (idclientes,arts,monto,iva,total,
            saldo,fecha,fechapago,tipovta,usu,status,coment,facturar,tpago)
            VALUES(1,$totarts,$montot,$totiva,$total,$saldo,'$fechaconv','$fpago',
            $tventa,'$usu',$status,'$cte',$most,$tpagom)");          
            if($query){
                //numero de pedido
                $pedido=traepedmax($mysqli); 
                //CICLO POR CADA ARTICULO DEL PEDIDO para suma de ventas por tasa
                $i= 0;
                foreach($arts as $id){
                    $idact=$arts[$i][0];
                    $caact=$arts[$i][1];
                    $pract=$arts[$i][2];
                    $moact=$arts[$i][3];
                    $ivact=$arts[$i][4];
                    $pesoact=$arts[$i][5];
                    
                    if($ivact==0){
                        //suma de ventas tasa0
                        array_push($vtas0,$moact);
                    }else{
                        //suma de ventas tasa16
                        array_push($vtas16,$moact);
                    }
//alta de articulos del pedido
                    $query2=$mysqli->query("INSERT INTO artsped (idpedido,idproductos,cant,preciou,preciot,status)
					VALUES ($pedido,$idact,$caact,$pract,$moact,99)");
                    if($query2){
 //calculo del costo y cantidad  para inventario. si el peso es 1,no se calcula segun peso
                        $umulti;
                        if($pesoact==1){$umulti=$caact;}else{$umulti=$pesoact;};
                        //afectacion a inventario
                        $query3=$mysqli->query("INSERT INTO inventario(idproductos,tipomov,cant,
                        fechamov,usu,idoc,factu,haber)
					    SELECT $idact,2,$umulti,'$fechaconv','$usu',$pedido,$most,
                        (costov*$umulti) FROM productos 
                        WHERE idproductos = $idact");
                        if(!$query3){
                            $jsondata['errorsql']= mysqli_error($mysqli);
                            throw new Exception("en alta inventario",4);                       
                        }

                    }else{
                        $jsondata['errorsql']= mysqli_error($mysqli);
                        throw new Exception("en alta arts pedido",3);
                    }
                    $i++;
                }
                //FIN DEL CICLO ARTICULOS
                
                //totalizacion de ventas por tasa
                $sventas0=array_sum($vtas0);
                $sventas16=array_sum($vtas16);
                //afectacion a diario
                //no se esta enviando ieps hasta que mostrador se prepare para ello
                $resulv=venta($mysqli,$fechaconv,$pedido,$sventas16,$sventas0,$totiva,$tpago,
                    $most,1);
                if($resulv==0){
                    $jsondata['ped']=$pedido;
                    $jsondata['tpago'] = $tpagom;
                    $jsondata['resul'] = $resul;
                    //efectuar la operacion
                    $mysqli->commit();
                }else{
                    $jsondata['errorsql']= mysqli_error($mysqli);
                    throw new Exception("en movtos diario",5);
                }
                
            }else{
                $jsondata['errorsql']= mysqli_error($mysqli);
                throw new Exception("en alta pedido",2);
            }
            
        }catch(Exception $ex){
            $jsondata['resultado']=$ex->getCode();
            $jsondata['mensaje']=$ex->getMessage();
            $jsondata['errorsql']= mysqli_error($mysqli);
            $mysqli->rollback();
        }finally{
            $mysqli->close();
            //fin de las transacciones//   
        }  	
   }else{
       $jsondata['errorsql'] =mysqli_connect_error();
       throw new Exception("en conexion bd",1);
			}
//salida
   		echo json_encode($jsondata);	
   
    
