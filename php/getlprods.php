<?php
/*** Esta rutina obtiene datos de productos para mostrar en tablas ***/
	/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
	$nivel;
/*** obtiene lista de productos del nivel establecido si lo hay ***/
if(isset($_POST['niv'])){$precio="precio".$_POST['niv']; }else{$precio = "costo"; }

/**trae descripcion  y precio de productos no cancelados del nivel requerido**/
    if (is_object($mysqli)) {
    	$sqlCommand = "SELECT descripcion,$precio,grupo FROM productos WHERE 1 ORDER BY GRUPO,descripcion";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE SELEC PROD. ".mysqli_error($mysqli));
//inicializacion de arreglo
			 while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 	$result[] = array('grupo' => $tempo['grupo'],'desc' => $tempo['descripcion'],'precio' => $tempo[$precio],'costo'=>$tempo['costo']);
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