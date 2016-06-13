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
				 $table= "productos";
				  $prov = $_POST['selectmenu'];
				  $grupo =$_POST['selectmenu2'];
				  $nombre = strtoupper($_POST['nombre']);
				  $nomcor = strtoupper($_POST['nomcor']);
				  $cod = strtoupper($_POST['cod']);
				  $ud =$_POST['selectmenu3'];
				  $cant =$_POST['cant'];
				  $barr =$_POST['barr'];				  
				  $cost =$_POST['cost'];
				  $desc = strtoupper($_POST['desc']);
				  $p1 =$_POST['p1'];
				  $p2 =$_POST['p2'];
				  $p3 =$_POST['p3'];
				  $usu = $_SESSION['usuario'];	
		//insercion en la tabla de productos

	   		$sqlCommand= "INSERT INTO $table (codigo,cbarras,nombre,nom_corto,
	   		grupo,unidad,cant,descripcion,costo,precio1,precio2,precio3,idproveedores,usu,status)
	    	VALUES ('$cod','$barr','$nombre','$nomcor',$grupo,$ud,$cant,'$desc',$cost,'$p1','$p2','$p3',$prov,'$usu',0)";

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

