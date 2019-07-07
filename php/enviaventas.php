<?php


if(isset($_POST['dat'])){
    $posted_data = $_POST['dat'];
    $data = json_decode($posted_data);
    extract((array)$data);
    /*** Autoload class files ***/
    function __autoload($class){
        require('../include/' . strtolower($class) . '.class.php');
    }
    //funciones auxiliares
    require '../include/funciones.php';
    $funcbase = new dbutils;
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
        /**inicializa variable resultado**/
    	$result2=0;
    	/*** checa login***/
    	$funcbase->checalogin($mysqli);	
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
    	
    	function cstatus($tipoventa,$facturar){
    	    //asigna el status del pedido  y articulos de acuerdo al tipo de venta
    	    //para el alta del pedido
    	    // SI SE VA A FACTURAR
    	    if($facturar==1){
    	        switch ($tipoventa) {
    	            //mostrador
    	            case 0:
    	                //X FACTURAR
    	                $pdst= 40;
    	                //surtido
    	                $arst=99;
    	                break;
    	                //contado
    	            case 1:
    	                //X FACTURAR
    	                $pdst= 40;
    	                //xsurtir OJO
    	                $arst=99;
    	                break;
    	            case 2:
    	                //credito
    	                //CXC
    	                $pdst= 30;
    	                //xsurtir
    	                $arst=99;
    	                break;
    	        }
    	    }else{
    	        //SI NO SE VA A FACTURAR
    	        switch ($tipoventa) {
    	            //mostrador
    	            case 0:
    	                //PAGADO
    	                $pdst= 40;
    	                //surtido
    	                $arst=99;
    	                break;
    	                //contado
    	            case 1:
    	                //AL COBRO S FACT cuando se envia merc al cobro y se espera recibir efec
    	                $pdst= 20;
    	                //xsurtir
    	                $arst=99;
    	                break;
    	            case 2:
    	                //credito
    	                //X COBRAR
    	                $pdst= 30;
    	                //xsurtir
    	                $arst=99;
    	                break;
    	        }
    	        
    	    }
    	    
    	    return array($pdst,$arst);
    	}
    	//creacion de datos para el pedido
    	$jsondata = array();
        $vtas16= array();
        $vtas0=array();
        //recoleccion de variables
        $fechaconv = converfecha($fecha);
        $tventa=$tipoventa;
        //se decide si hay fecha pago
        $fpago;
        $saldo;
        //determinacion de saldo
        if($tventa!=2){$fpago=$fecha;$saldo=0;}else{$fpago=NULL;$saldo=$total;}
        $facturar=$facturarp;
        $facturarb=cambiafact($facturar);
        $usu=$_SESSION['usuario'];
        $status= cstatus($tventa,$facturarb);
        $statusp=$status[0];
        $statusa=$status[1];
        $pedido;
        //arreglo de productos
        $arts=$prods;
        //afectacion a bd
        //alta de pedido
        $sqlCommand= "INSERT INTO pedidos(idclientes,arts,monto,iva,total,saldo,fecha,tipovta,usu,status,facturar,fechapago,factura,arch)
	   VALUES ($cte,$totarts,$montot,$totiva,$total,$saldo,'$fechaconv',$tventa,'$usu',$statusp,$facturarb,'$fpago','$sefo','$arch')";
        $query= mysqli_query($mysqli, $sqlCommand)or die("error en alta pedidos:".mysqli_error($mysqli));
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
                $ivact=$arts[$i][4];
                
                if($ivact==0){
                    //suma de ventas tasa0
                    array_push($vtas0,$moact);
                }else{
                    //suma de ventas tasa16
                    array_push($vtas16,$moact);
                }
                
                //alta de articulos del pedido
                $sqlCommand2= "INSERT INTO artsped (idpedido,idproductos,cant,preciou,preciot,status)
					VALUES ($pedido,$idact,$caact,$pract,$moact,$statusa)";
                //calculo del costo. si el peso es 1,no se calcula segun peso
                $query2=mysqli_query($mysqli, $sqlCommand2)or die("error en alta artsped:".mysqli_error($mysqli));
                if($query2) {
                    //afectacion a inventario de acuerdo a tipo de articulo
                    $cantif=$caact;
                    $sqlCommand3= "INSERT INTO inventario (idproductos,tipomov,cant,fechamov,usu,idoc,factu,haber)
					SELECT $idact,2,$cantif,'$fechaconv','$usu',$pedido,$facturarb,(costov*$caact) FROM productos WHERE idproductos = $idact";
                    $query3=mysqli_query($mysqli, $sqlCommand3)or die("error en salida invent: ".mysqli_error($mysqli));
                    if(!$query3){
                        $result2=-3;
                    }
                }else{$result2=-2;}
                
                $i++;
            }
            
            //totalizacion de ventas por tasa
            $sventas0=array_sum($vtas0);
            $sventas16=array_sum($vtas16);
            //afectacion a diario
            $resul2=venta($mysqli,$fechaconv,$pedido,$sventas16,$sventas0,$totiva,$tventa,$facturarb,$cte);
            if($resul2!=0){$result2=-3;};
            
        }else {$result2=-1;}
        
    }else{$result2=-99;}
    //creacion de variables de respuesta
    $jsondata['result2']=$result2;
    $jsondata['ped']=$pedido;
    $jsondata['tventa']=$tventa;
    //salida
    mysqli_close($mysqli);
}else{$result2= -1000;};
echo json_encode($jsondata);  
