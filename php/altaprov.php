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
				//asignacion de variables
				 $table= "proveedores";
				  $razon = strtoupper($_POST['razon']);
				  $rfc = strtoupper($_POST['rfc']);
				  $nomcor = strtoupper($_POST['nomcor']);
				  $dir = strtoupper($_POST['dir']);
				  $telef = $_POST['telef'];
				  $cont = strtoupper($_POST['cont']);			  
				  $usu = $_SESSION['usuario'];
				  	
		//insercion en la tabla de proveedores  	

	   		$sqlCommand= "INSERT INTO $table (razon_social,rfc,nom_corto,direccion,tel,contacto,usu,status)
	    	VALUES ('$razon', '$rfc','$nomcor','$dir','$telef','$cont','$usu',0)";

	    	$query=mysqli_query($mysqli, $sqlCommand) or die (mysqli_error($mysqli)); 
			
			if($query){
		echo 0;
		}
		else{echo 1;}		
			/* cerrar la conexion */
	    	mysqli_close($mysqli);  
			exit();
			
	} else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
?>

