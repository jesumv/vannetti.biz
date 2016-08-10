<?php

	/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
/**trae id y no corto de clientes no cancelados**/
    if (is_object($mysqli)) {
    	$sqlCommand = "SELECT idclientes,nom_corto FROM clientes  WHERE status < 2 ORDER BY nom_corto";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE SELEC CTES. ".mysqli_error($mysqli));
//inicializacion de arreglo
			 while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('nombre' => $tempo['nom_corto'],'id' => $tempo['idclientes']);
			 };

			sort($result);
	/* liberar la serie de resultados */
			  mysqli_free_result($query1);			  
	/* cerrar la conexi�n */
	  mysqli_close($mysqli);
	  
	 echo json_encode($result);
	 
	}else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
?>