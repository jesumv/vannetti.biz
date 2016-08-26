<?php
	/**
	 * esta clase se usa para operaciones de base de datos
	 */
	 
	 /**
	  * 
	  */ 
	
	class dbutils  {
		/*** la tabla a leer ***/
		public $table;
		function __construct() {
			
		}
		
	   public function conecta() {
	    /***esta funcion establece la conexion a sql***/
		/***variables de conexion ***/
		$mysql_hostname = "localhost";
		$mysql_user = "root";
		$mysql_password = "";
		$mysql_database = "ventas";


		$mysqli = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
		if($mysqli->connect_errno > 0){
		    die('No se establecio conexion a la base de datos [' . $mysqli->connect_error . ']');
			return -1;
		}else{
				if(!$mysqli->set_charset("utf8")) {
    				die("Error cargando el conjunto de caracteres utf8: ". $mysqli->error);
				}else{
					return $mysqli;
				}
		
			}
    
	   }
        public function checalogin($mysqli){
         //***checa si el cliente esta registrado ***/
         //obtiene el path absoluto
            session_start();
    
            $user_check=$_SESSION['usuario'];
            
            $ses_sql=mysqli_query($mysqli,"select username from usuarios where username='$user_check' ");      
            $row=mysqli_fetch_array($ses_sql);
            
            $login_session=$row['username'];
    
            if(!isset($login_session))
            {
                  header("Location:/vannetti.biz/logout.html"); 
               
            }
        }

        
        public function leetodos($mysqli,$table,$filtro='1'){
          //***lee todos los datos de una tabla, un registro o todos los registros, de acuerdo con el argumento $filtro ***/
            $sql= "SELECT * FROM $table WHERE ".$filtro;
            $result = mysqli_query($mysqli,$sql);
            $result2 = mysqli_fetch_row($result);
            /* liberar la serie de resultados */
                  mysqli_free_result($result);
                  /* cerrar la conexion */
                  mysqli_close($mysqli);
            if($result2){
              return $result2;  
            }
            else {
                 die('no hay resultados para '.$table);
            }
        }
		
		public function leelprod($mysqli,$nivel=3){
          //***lee todos los datos de una tabla, un registro o todos los registros, de acuerdo con el argumento $filtro ***/
          if ($nivel ==3) {
               $nprecio = precio."3";
          } elseif ($nivel==0){
              $nprecio = "costo";
          }else{
          	 $nprecio = precio."$nivel";
          }
          
         
            $sqlCommand = "SELECT codigo,nombre,".$nprecio." FROM productos WHERE status < 1 ORDER BY nombre";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE SELEC PROD. ".mysqli_error($mysqli));
//inicializacion de arreglo
				while($tempo=mysqli_fetch_array($query1)){
			 		$result[] = array('cod' => $tempo['codigo'],'desc' => $tempo['nombre'],'precio' => $tempo[$nprecio]);
			 };
            /* liberar la serie de resultados */
                  mysqli_free_result($query1);
                  /* cerrar la conexion */
                  mysqli_close($mysqli);
            if($result){
              return $result;  
            }
            else {
                 die('no hay resultados para ');
            }
        }
		
		public function leeuno($mysqli,$table,$filtro){
          //***lee un registro de una tabla, un registro o todos los registros, de acuerdo con el argumento $filtro ***/
            $sql= "SELECT * FROM $table WHERE ".$filtro;
            $result = mysqli_query($mysqli,$sql);
            $result2 = mysqli_fetch_row($result);
            /* liberar la serie de resultados */
                  mysqli_free_result($result);
                  /* cerrar la conexion */
                  mysqli_close($mysqli);
            if($result2){
              return $result2;  
            }
            else {
                 die('no hay resultados para '.$table);
            }
        }
		
		
        
        public function numremi($mysqli){
        	/*lee el ultimo numero de remision emitido*/
        	$sql= "SELECT MAX(idremisiones) FROM remisiones";
            $result = mysqli_query($mysqli,$sql);
            $result2 = mysqli_fetch_row($result);
			$dato = $result2[0];
            if($result2){
              return $dato;  
            }
            else {
                 die('no hay resultados para el numero de remision');
            }
        }
		
		
		
		
		public function numinv($mysqli){
			//lee el ultimo movimiento del inventario. esta funcion no ha sido aplicada, porque marco inexistente, revisar
			$sql= "SELECT MAX(idinventarios) FROM inventarios";
            $result = mysqli_query($mysqli,$sql);
            $result2 = mysqli_fetch_row($result);
			$dato = $result2[0];
            if($result2){
              return $dato;  
            }
            else {
                 die('no hay resultados para la consulta de movimiento de inventario');
            }
			
		}
		
		public function leeprov($mysqli,$idprov){
			//lee el nombre corto del proveedor cuyo id se pasa como parametro
			
			$sql= "SELECT nom_corto FROM proveedores WHERE idproveedores = $idprov LIMIT 1";
			$result = mysqli_query($mysqli,$sql);
            $result2 = mysqli_fetch_row($result);
			$dato = $result2[0];
			
            if($result2){
            	/* liberar la serie de resultados */
                  mysqli_free_result($result);
                  /* cerrar la conexion */
                  mysqli_close($mysqli);
              	return $dato;  
            }
            else {
                 die('no hay resultados para consulta de nombre proveedor');
            }
			
		}
		
		
		public function compledom($calleno,$col,$del,$ciudad,$estado,$cp){
			/*toma los elementos del domicilio y los une en una cadena */
			$completo = $calleno." ".$col." ".$del." c.p.".$cp." ".$ciudad.", ".$estado;
			return $completo;
			
		}
        	
		public function llenaarts($mysqli,$remision,$codigo,$descripcion,$precio,$cantidad,$importe){
			/*llena la tabla artremision con los articulos de cada remision*/
			$table = 'artremision';
			$sqlCommand= "INSERT INTO $table (codigo,remision,descripcion,precio_unitario,cantidad,importe)
	    	VALUES ($codigo,$remision,'$descripcion',$precio,$cantidad,$importe)"
	    or die('insercion cancelada '.$table);
			
	    // Execute the query here now
	    if($query=mysqli_query($mysqli, $sqlCommand)){
	    	return 0;
	    }else{
	    	return $mysqli->connect_error;
	    }
	    
			
		}
		
	
	}/*** fin de la clase ***/
	
	class otrasdbutils{
		function __construct() {
			
		}
		
		public function ultcliente($mysqli){
			/*esta funcion trae el ultimo numero de cliente registrado */
			$sql= "SELECT MAX(idclientes) FROM clientes";
			$result = mysqli_query($mysqli,$sql);
            $result2 = mysqli_fetch_row($result);
			$dato = $result2[0];
            if($result2){
              return $dato;  
			}
			
			 /* liberar la serie de resultados */
	   				mysqli_free_result($result); 	
		}
		
		public function ultcliente2($mysqli){
			/*esta funcion trae el ultimo numero de cliente registrado y su nombre corto */
			$sql= "SELECT MAX(idclientes)FROM clientes";
			$result = mysqli_query($mysqli,$sql);
            $result2 = mysqli_fetch_row($result);
			$cliente = $result2[0];
			$sql2 = "SELECT idclientes, nom_corto FROM clientes WHERE idclientes =".$cliente;
			$result3 = mysqli_query($mysqli,$sql2);
            $result4 = mysqli_fetch_row($result3);
			 /* liberar la serie de resultados */
	   		mysqli_free_result($result); 
			return $result4; 	
		}
		
		public function nomcliente($mysqli,$idclientes){
			/*esta funcion trae el nombre corto del cliente proporcionado*/
			$sql= "SELECT nom_corto FROM clientes where idclientes =".$idclientes;
			$result = mysqli_query($mysqli,$sql);
            $result2 = mysqli_fetch_row($result);
			 /* liberar la serie de resultados */
	   		mysqli_free_result($result); 
			return $result2; 	
		}
		
		
		
		public function estadorem($tipo, $estado){
			/* esta funcion determina el estado de una remision en funcion de los parametros */
			if ($tipo == 2) {
				$resul = "EN BLANCO";
			}else{
							switch ($estado) {
			    case 5:
			      $resul = "CANCELADA";
			        break;
			    case 10:
			        $resul = "FACTURADA";
			        break;
				default:
			       	$resul = "VIGENTE";
			        break;
			}
				
			}
			
			return $resul;
		}
		
		public function cancelarem($mysqli,$cancelada,$nueva){
			/* esta funcion recibe un numero de remision, y cambia su estado a 5 cancelada x substitucion */
			$rollo = "ESTA REMISION SE SUBSTITUYE X LA NO. ".$nueva;
			$sqlCommand = "UPDATE remisiones SET status =5, obser= '$rollo' WHERE idremisiones= $cancelada LIMIT 1";
			 // Execute the query here now
	    	$query = mysqli_query($mysqli, $sqlCommand) or die ("error en actualizacion estado ".mysqli_error($mysqli)); 
			if(mysqli_errno($mysqli)){
				return mysqli_errno($mysqli);
			}else{return 0;}		
		}
		
		public function consultaremi($mysqli,$remi,$nivel,$remitido){
			/*esta funcion consulta los articulos de una remision*/
			/*recibe como parametros el numero de remision y el nivel del cliente*/
			//CONSTANTES---------------------------------------------------------------------------------------------------------
    		//arreglo para la lista de articulos
        	$arts= array();
			//determinacion del precio del producto
			$preciorev= "t2.precio".$nivel;
			$query= "SELECT  t1.codigo,t2.descripcion,".$preciorev.",t1.cantidad,t1.importe,t2.alg FROM artremision as t1 
			left join productos as t2 on t1.codigo=t2.codigo WHERE  remision=".$remi;
			$datoart= mysqli_query($mysqli,$query)or die ("Error en la consulta de articulos de la remision.".mysqli_error($mysqli));
			$indice = 0;
			while ($fila = mysqli_fetch_row($datoart)) {
			       $arts[$indice][0]= $fila[0];
				if ($remitido==2){
					$arts[$indice][1] = $fila[1]." ALG:".$fila[5];
				}else{
					$arts[$indice][1] = $fila[1];
				}
			      
			       $arts[$indice][2]= $fila[2];
				   $arts[$indice][3] = $fila[3];
				   $arts[$indice][4] = $fila[4];
				   $arts[$indice][5] = $fila[5];
			       $indice++;
			    }
					return $arts;	
						
			}
		
		
	}/*** fin de la clase ***/
	
?>