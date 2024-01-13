<?php
/*** Autoload class files ***/ 
function myAutoload($ClassName)
{
    require('../include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

	
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
    	/*** checa login***/
    	$funcbase->checalogin($mysqli);
	
    //recoleccion de variables
    $oc= $_POST["oc"];
	$prov= $_POST["prov"];
    $coment = $_POST["coment"];
	$banco= $_POST["banco"];
	$fac= $_POST["fac"];
	$fol= $_POST["fol"];
	$monto=$_POST["monto"];
	$iva= $_POST["iva"];
	$total =$_POST["total"];

	//creacion de datos para el pedido
	$usu= $_SESSION['usuario'];
	
	//estandarizacion de banco
		switch ($banco) {
    case "CAJA":
        $cbanco= 1;
        break;
    case "BANCOMER":
        $cbanco=2;
        break;
    default:
        $cbanco=0;
} 

		//1 actualizacion  de oc 
		$sqlCommand= "UPDATE oc SET usu='$usu',status= 99,coment='$coment',fact='$fac', foliomov=$fol
		 WHERE idoc = $oc";
			$query=mysqli_query($mysqli, $sqlCommand) or die (mysqli_error($mysqli)); 
				if($query){
					$result= 0;
		//si el resultado de las anteriores operaciones es ok, se continua con los movimientos contables
		//2 caja o bancos
					$sqlCommand= "INSERT INTO disponible (idmovto,idproveedores,cuenta,usu,haber)
					VALUES ($oc,$prov,$cbanco,'$usu',$total)";
					$query=mysqli_query($mysqli, $sqlCommand)or die("error en abono caja: ".mysqli_error($mysqli));
					if(!$query){$result=-2;}else{
						//3 abono a cuentas por pagar
						$sqlCommand= "INSERT INTO cxp (idmovto,idproveedores,monto,iva,total,usu,debe)
						VALUES ($oc,$prov,$monto,$iva,$total,'$usu',$total)";
						$query=mysqli_query($mysqli, $sqlCommand)or die("error en abono cxp: ".mysqli_error($mysqli));
						if(!$query){
							$result = -3;	
						}else{
							//4 modificacion de ivaxacreditar
							$sqlCommand= "INSERT INTO ivaacred (idmovto,idproveedores,fact,usu,status,haber)
							VALUES ($oc,$prov,'$fac','$usu',1,$iva)";
							$query=mysqli_query($mysqli, $sqlCommand)or die("error en abono ivax acred: ".mysqli_error($mysqli));
							if(!$query){
								$result = -4;	
							}else{
							$sqlCommand= "INSERT INTO ivaacred (idmovto,idproveedores,fact,usu,status,debe)
							VALUES ($oc,$prov,'$fac','$usu',0,$iva)";
							$query=mysqli_query($mysqli, $sqlCommand)or die("error en cargo iva acred: ".mysqli_error($mysqli));
							if(!$query){$result = -5;}
							}
							
						}
					}

				}else{$result=-1;}			
			
			/* cerrar la conexion */
			mysqli_close($mysqli);  
			
			//salida de respuesta
			echo $result;
	} else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }