<?php
   /*** Autoload class files ***/ 
function myAutoload($ClassName)
{
    require('include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

//directiva a la conexion con base de datos
/*** conexion a bd ***/
    
    $resul;
    $mal=null;
    $funcbase = new dbutils;
    $mysqli = $funcbase->conecta();
	
    if (is_object($mysqli)) {
	/*** checa login***/
	        $funcbase->checalogin($mysqli);
				//asignacion de variables
	             $jsondata = array();
				 $table= "proveedores";
				  $razon = strtoupper($_REQUEST['razon']);
				  $rfc = strtoupper($_REQUEST['rfc']);
				  $nomcor = strtoupper($_REQUEST['nomcor']);
				  $dir = strtoupper($_REQUEST['dir']);
				  $telef = $_REQUEST['telef'];
				  $cont = strtoupper($_REQUEST['cont']);
				  $correo=strtoupper($_REQUEST['correo']);
				  $fact=$_REQUEST['factura'];
				  $dcred =$_REQUEST['dcred'];
				  $usu = $_SESSION['usuario'];
		//normalizacion de datos
				  $factura = ($fact=="true") ? 1 : 0;
				  	
		//insercion en la tabla de proveedores  	

	   		$sqlCommand= "INSERT INTO $table (razon_social,rfc,nom_corto,direccion,tel,
            contacto,usu,status,email,factura,diascred)
	    	VALUES ('$razon', '$rfc','$nomcor','$dir','$telef','$cont','$usu',0,'$correo',$factura,$dcred)";
	   		try{
	   		    $query=mysqli_query($mysqli, $sqlCommand) or die (mysqli_error($mysqli));
	   		    if($query){$resul=0;}
	   		}catch(Exception $e){
	   		    $mal=$e->getMessage();
	   		    $resul= –1;
	   		}	    	 		
			/* cerrar la conexion */
	    	mysqli_close($mysqli);
	    	$jsondata['resul'] =$resul;
	    	$jsondata['mal'] =$mal;
			
	} else {
	    $mal=$mysqli->connect_error;
	    $resul=-1;
    }
    echo json_encode($jsondata); 
    
