<?php
/*** Esta rutina obtiene los productos de una orden de compra***/
	/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
/*** obtiene grupo si lo hay ***/
	/**trae datos productos para el grupo**/
    if (is_object($mysqli)) {
    	if(isset($_GET['idcte'])){
    		$cte = $_GET['idcte']; 
			//obtencion de nivel cliente
			$sqlnivel = "SELECT nivel FROM clientes WHERE idclientes = $cte";
			// Execute the query here now
			$query2=mysqli_query($mysqli, $sqlnivel) or die ("ERROR EN CONSULTA DE NIVEL ".mysqli_error($mysqli));
			$row = mysqli_fetch_assoc($query2);
			$nivel = $row['nivel'];
			//adecuacion de campo de precio, para el caso de clientes al costo.
			if($nivel == 0){$nivelcor ='costo';}else{$nivelcor ='precio'.$nivel;}
			$sqlCommand = "SELECT t1.grupo,t1.nom_corto,t1.idproductos,$nivelcor,t1.iva,t1.spesov,t1.cant,t2.nombre AS unidad FROM productos AS t1 
			INNER JOIN unidades AS t2 ON t1.unidad= t2.idunidades WHERE t1.status = 0 ORDER BY grupo, nom_corto";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE PRODS PEDL. ".mysqli_error($mysqli));
//inicializacion de arreglo
			$filas = $query1->num_rows;
			if($filas > 0){
				while($tempo=mysqli_fetch_array($query1, MYSQLI_ASSOC)){
			 		$result[] = array('gpo'=>$tempo['grupo'],'idprod' => $tempo['idproductos'],'nombre' => $tempo['nom_corto'],
			 		'precio'=>$tempo[$nivelcor],'iva'=>$tempo['iva'],'spesov'=>$tempo['spesov'],'presen'=>$tempo['cant'],'ud'=>$tempo['unidad']);
			 	}
			}else{$result= 1;}
			 
	/* liberar la serie de resultados */
			  mysqli_free_result($query1);	
		}else{$result = 2; }
    	
	}else{die ("<h1>'No se establecio la conexion a bd'</h1>");};
    		  
	/* cerrar la conexi�n */

	 mysqli_close($mysqli);
	 echo json_encode($result);	 	
