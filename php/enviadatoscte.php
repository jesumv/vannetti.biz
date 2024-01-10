<?php
   /*** Autoload class files ***/ 
function myAutoload($ClassName)
{
    require('include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

	
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
	$resul = 0;
    if (is_object($mysqli)) {
	/*** checa login***/
	        $funcbase->checalogin($mysqli);
				//asignacion de variables
				 $table= "clientes";
				 $usu= $_SESSION['usuario'];
				 $razon = strtoupper($_POST['razon']);
				 $rfc = strtoupper($_POST['rfc']);
				 $corto= strtoupper($_POST['corto']);
				 $calle= strtoupper($_POST['calle']);
				 $noext= strtoupper($_POST['noext']);
				 $noint= strtoupper($_POST['noint']);
				 $cp = strtoupper($_POST['cp']);
				 $col = strtoupper($_POST['col']);
				 $mun = strtoupper($_POST['mun']);
				 $edo = strtoupper($_POST['edo']);
				 $tel1 = strtoupper($_POST['tel1']);
				 $tel2 = strtoupper($_POST['tel2']);
				 $telc = strtoupper($_POST['telc']);
				 $cont = strtoupper($_POST['cont']);
				 $corr = $_POST['corr'];
				 $nivel = strtoupper($_POST['nivel']);
				 $diasc = strtoupper($_POST['diasc']);
				  
				  	
		//insercion en la tabla de proveedores  	

	   		$sqlCommand= "INSERT INTO $table (razon_social,rfc,nom_corto,domcalle,domnoext,domnoint,domcp,domcol,dommun,domedo,tel1,
	   		tel2,cel,contacto,email,nivel,usu,diascred,status)
	    	VALUES ('$razon', '$rfc','$corto','$calle','$noext','$noint','$cp','$col','$mun','$edo','$tel1',
	    	'$tel2', '$telc','$cont','$corr','$nivel','$usu','$diasc',1)";

	    	$query=mysqli_query($mysqli, $sqlCommand) or die ('error en alta cliente: '.mysqli_error($mysqli)); 
			
			if(!$query){
			$resul = 1;
		}
			/* cerrar la conexion */
	    	mysqli_close($mysqli); 
			//salida
			$jsondata['resul'] = $resul;
   			echo json_encode($jsondata);	 
			exit();
			
	} else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
?>

