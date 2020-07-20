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
    //creacion de datos para el pedido
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
            //para cada impuesto
            $ivat=array();
            $iepst=array();
            //recoleccion de variables
            //se decide si hay fecha pago
            //definicion de variables
            $cte=$_POST["cte"];
            $fecha= $_POST["fecha"];
            $fechaconv = converfecha($fecha);
            $tventa=$_POST['tipoventa'];
            $facturar=$_POST["facturarp"];
            //cambiar variable a booleano
            $facturarb=cambiafact($facturar);
            $totarts=$_POST["totarts"];
            $montot= $_POST["montot"];
            $totimps=$_POST["totimps"];
            $total=$_POST["total"];
            $usu= $_SESSION['usuario'];
            $resulp=datosppago($total,$tventa,$fechaconv);
            $fpago=$resulp['fpago'];
            $tvtam= $resulp['tipovta'];
            $status= $resulp['status'];
            $saldo= $resulp['saldo'];
            $tpagom= $resulp['tpago'];
            $statusp=$resulp['status'];
            $statusa=$resulp['statusa'];
            $pedido;
            //arreglo con datos de producto
            $arts=$_POST['prods'];
            //afectacion a bd
            //alta de pedido
            $table="pedidos";
            $mysqli->autocommit(false);
            $query=$mysqli->query("INSERT INTO $table (idclientes,arts,monto,iva,total,
            saldo,fecha,fechapago,tipovta,usu,status,facturar,tpago)
            VALUES($cte,$totarts,$montot,$totimps,$total,$saldo,'$fechaconv','$fpago',
            $tvtam,'$usu',$status,$facturarb,$tpagom)");           
            if($query){
                //numero de pedido
                $pedido=traepedmax($mysqli);
                //CICLO POR CADA ARTICULO DEL PEDIDO
                $i= 0;
                foreach($arts as $id){
                    $idact=$arts[$i][0];
                    $caact=$arts[$i][1];
                    $pract=$arts[$i][2];
                    $moact=$arts[$i][3];
                    $impsact=$arts[$i][4];
                    $ivact=$arts[$i][5];
                    $iepsact=$arts[$i][6];
                    $presact=$arts[$i][7];
                    $pesoact=$arts[$i][8];
                    $spesovact=$arts[$i][9];
                    $precioact=$presact*$caact;
                    
                    if($ivact==0){
                        //suma de ventas tasa 0
                        array_push($vtas0,$moact);
                    }else{
                        //lleva iva
                        //suma de ventas tasa16
                        array_push($vtas16,$moact);
                        array_push($ivat,($moact*.16));
                    } 
                    if($iepsact==1){
                        //leva ieps
                        array_push($iepst,($moact*.08));
                    }
                    //alta de articulos del pedido
                    $sqlCommand2= "INSERT INTO artsped (idpedido,idproductos,cant,preciou,preciot,status)
					VALUES ($pedido,$idact,$caact,$pract,$moact,$statusa)";
                    $query2=$mysqli->query($sqlCommand2);
                    //calculo del costo. si el peso es 1,no se calcula segun peso
                    if($pesoact==1){$umulti=($caact);}else{$umulti=($pesoact);};
                    if($query2){
                        //afectacion a inventario de acuerdo a tipo de articulo
                        if($spesovact==0){$cantif=$caact;}else{$cantif=$pesoact;}
                       $sqlCommand3= "INSERT INTO inventario (idproductos,tipomov,cant,fechamov,usu,idoc,factu,haber)
					   SELECT $idact,2,$cantif,'$fechaconv','$usu',$pedido,$facturarb,(costov*$umulti) 
                       FROM productos WHERE idproductos = $idact";
                       $query3=$mysqli->query($sqlCommand3);
                       if(!$query3){
                           throw new Exception("en salida inventario",4);                         
                       }                      
                    }else{
                        throw new Exception("en alta arts",3);
                    }                   
                    $i++;
                }
      //FIN DEL CICLO ARTICULOS
      //totalizacion de ventas por tasa
                $sventas0=array_sum($vtas0);
                $sventas16=array_sum($vtas16);
      //totalizacion de impuestos
                $ivatt=array_sum($ivat);
                $iepstt=array_sum($iepst);
                //afectacion a diario
                $resul2=venta($mysqli,$fechaconv,$pedido,$sventas16,$sventas0,$ivatt,$tventa,$facturarb,$cte,$iepstt);
                if($resul2==0){
                    //creacion de variables de respuesta
                    $jsondata['resul']=$resul;
                    $jsondata['ped']=$pedido;
                    $jsondata['tventa']=$tvtam;
                    //efectuar la operacion
                    $mysqli->commit();
                }else{
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
            mysqli_close($mysqli);
        }
    		
    }else{
     $jsondata['errorsql'] =mysqli_connect_error();;
    throw new Exception("en conexion bd",1);}
  
   //salida
   echo json_encode($jsondata);
   
    
