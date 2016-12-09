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
		
	function traeoc($mysqli){
		$req = "SELECT MAX(idoc) FROM oc where 1"; 
    	$result = mysqli_query($mysqli,$req);
		$row=mysqli_fetch_array($result,MYSQLI_NUM);
	 /* liberar la serie de resultados */
	    mysqli_free_result($result);
		return $row[0];
	}
	
    //recoleccion de variables
    $prov = $_POST["prov"];
	$longi = $_POST["longi"];
	$cants=$_POST["cants"];
	$prods=$_POST["prods"];
	$preciou = $_POST["preciou"];
	$preciot=$_POST["preciot"];
	$total=$_POST["total"];
	$jsondata = array();
	//creacion de datos para oc
	$arts= array_sum($cants);
	$usu= $_SESSION['usuario'];
	$iva=0;
	if(isset($_POST['cred'])){$credito=1;}else{$credito=0;};
	if(isset($_POST['fact'])){$facturar=1;}else{$facturar=0;};
	//creacion de oc en tabla oc
	$sqlCommand= "INSERT INTO oc (idproveedores,arts,monto,total,usu,status,facturar,credito)
	    	VALUES ($prov,$arts,$total,$total,'$usu',1,$facturar,$credito)";
	    	$query=mysqli_query($mysqli, $sqlCommand)or die("error en alta oc:".mysqli_error($mysqli)); 
			if($query){				
				//obtencion de numero de orden de compra
				$noc = traeoc($mysqli);
				//insercion de productos en tabla artsoc
				$indi = 0;
					foreach($prods as $id){
						$sqlCommand= "INSERT INTO artsoc (idoc,idproductos,cant,preciou,preciot,status)
		    			VALUES ($noc,$prods[$indi],$cants[$indi],$preciou[$indi],$preciot[$indi],1)";
						$query=mysqli_query($mysqli, $sqlCommand)or die("error en alta artsoc:".mysqli_error($mysqli)); 	
						if($query){
							$jsondata['success'] = 0;
							$jsondata['noc'] = $noc;
							$jsondata['arts'] = $arts;
							$jsondata['total'] =$total;
						}else{$jsondata['success'] = -2;}
						$indi++;
					}
					}else{$jsondata['success'] = -1;}
			/* cerrar la conexion */
	    	mysqli_close($mysqli);  

	//salida de respuesta
		 echo json_encode($jsondata);
    }else{
    	
    }
