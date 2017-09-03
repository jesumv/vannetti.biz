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
    if (is_object($mysqli)) {
    	/**inicializa variable resultado**/
    	$resul=0;
    	/*** checa login***/
    	$funcbase->checalogin($mysqli);
	
	//creacion de datos para la venta de mostrador
	$jsondata = array();
	//para las ventas por tasa
	$vtas16= array();
	$vtas0=array();
    //recoleccion de variables
    $cte=$_POST["cte"];
	$fecha= $_POST["fecha"];
	$fechaconv = converfecha($fecha);
	$tventa=$_POST['tipoventa'];
	//cambiar variable a booleano
	$totarts=$_POST["totarts"];
	$montot= $_POST["montot"];
	$totiva=$_POST["totiva"];
	$total=$_POST["total"];
	$usu= $_SESSION['usuario'];
	$most= $_SESSION['mostrador'];
	//arreglo con datos de producto
	$arts=$_POST['prods'];
	//afectacion a bd
	
	try{
		$mysqli->autocommit(false);
		$table="pedidos";
		//insercion como pedido
			$mysqli->query("INSERT INTO $table(idclientes,arts,monto,iva,total,fecha,fechapago,tipovta,usu,status,facturar)
			VALUES ($cte,$totarts,$montot,$totiva,$total,'$fechaconv','$fechaconv',0,'$usu',40,$most)");
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
				$presact=$arts[$i][5];
				$pesoact=$arts[$i][6];
			
				if($ivact==0){
					//suma de ventas tasa0
					array_push($vtas0,$moact);
				}else{
					//suma de ventas tasa16
					array_push($vtas16,$moact);
				}
			//alta de articulos del pedido
					$mysqli->query("INSERT INTO artsped (idpedido,idproductos,cant,preciou,preciot,status)
					VALUES ($pedido,$idact,$caact,$pract,$moact,99)");
			//calculo del costo y cantidad  para inventario. si el peso es 1,no se calcula segun peso
			$umulti;
					if($pesoact==1){$umulti=$caact;}else{$umulti=$pesoact;};		
			//afectacion a inventario
					$mysqli->query("INSERT INTO inventario(idproductos,tipomov,cant,fechamov,usu,idoc,factu,haber)
					SELECT $idact,2,$umulti,'$fechaconv','$usu',$pedido,$most,(costov*$umulti) FROM productos WHERE idproductos = $idact")or die (mysqli_error($mysqli));		
				$i++;	
			}
	//FIN DEL CICLO ARTICULOS
	
			//totalizacion de ventas por tasa
				$sventas0=array_sum($vtas0);
				$sventas16=array_sum($vtas16);	
				//afectacion a diario
				$resul=venta($mysqli,$fechaconv,$pedido,$sventas16,$sventas0,$totiva,0,$most,$cte);			
			//efectuar la operacion
				$mysqli->commit();
	}catch (Exception $e) {
					//error en las operaciones de bd
				    $mysqli->rollback();
				   	$resul=-2;
				}
   //salida
   mysqli_close($mysqli);
   }else{
				$resul=-1;
			}
//salida
		$jsondata['ped']=$pedido;
		$jsondata['resul'] = $resul;
   		echo json_encode($jsondata);	
   
    
