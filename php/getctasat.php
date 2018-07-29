<?php

	/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
/**no cuenta y descr de catctasat**/
    if (is_object($mysqli)) {
    	$sqlCommand = "SELECT ventas.catctasat.NumCta,ventas.catctasat.Desc,ventas.catctasat.Natur FROM catctasat 
        WHERE mostrar = 1  ORDER BY ventas.catctasat.Desc";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE SELEC CTAS. ".mysqli_error($mysqli));
//inicializacion de arreglo
			 while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('numcta' => $tempo['NumCta'],'descri' => $tempo['Desc'],'natur' => $tempo['Natur']);
			 };

			sort($result);
	/* liberar la serie de resultados */
			  mysqli_free_result($query1);			  
	/* cerrar la conexion */
	  mysqli_close($mysqli);
	  
	 echo json_encode($result);
	 
	}else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
?>