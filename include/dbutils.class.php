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
		
		
		public function iva ($mysqli,$producto,$precio){
			//esta funcion calcula el iva para un monto dado
			//inicializar iva
				$iva=0;
			//revisar si el producto lleva iva
			$sql2= "SELECT iva FROM productos WHERE idproductos = $producto";
			$resulta = mysqli_query($mysqli,$sql2);
            if($resulta){
            	$row2 = mysqli_fetch_array($resulta);
					$result3=$row2['iva'];
					if($result3 == true){
					$sql= "SELECT valornum FROM parametros WHERE idparametros = 1";
	            	$result = mysqli_query($mysqli,$sql);
	            	$row = mysqli_fetch_array($result);
					$result2=$row['valornum'];
					$iva = $result2*$precio;
	            	/* liberar la serie de resultados */
	                  mysqli_free_result($resulta);
				}
              return $iva;  
            }
            else {
                 die('error en calculo iva ');	
			}
			
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
            
            $ses_sql=mysqli_query($mysqli,"select username from usuarios where username='$user_check'");      
            $row=mysqli_fetch_array($ses_sql);
            
            $login_session=$row['username'];
    
            if(!isset($login_session))
            {
                  header("Location:/vannetti.biz/logout.php"); 
               
            }
        }

       public function fechainic($mysqli){
	//establece la fecha de inicio para consultas de movimientos
	/*** obtiene el valor de la fecha maxima de cierre ***/
		$sql = "SELECT MAX(fechafin) FROM saldos;";
			// Execute the query here now
			$query=mysqli_query($mysqli, $sql) or die ("ERROR EN CONSULTA DE FECHA saldo MAXIMA ".mysqli_error($mysqli));
			$row = mysqli_fetch_row($query);
			$fechamax= new DateTime($row[0]);
			$fechamax->modify('+1 day');
			$result = $fechamax->format('Y-m-d');
			return $result;
}
	   
	   public function mostrador($mysqli){
	   	//establece el valor de facturacion para ventas mostrador
	   	$sql = "SELECT valorent FROM parametros WHERE idparametros = 3";
		$query=mysqli_query($mysqli, $sql) or die ("ERROR EN CONSULTA DE PARAMETROS INIC ".mysqli_error($mysqli));
		$row = mysqli_fetch_row($query);
		$result = $row[0];
		return $result;	
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
               $nprecio = "precio"."3";
          } elseif ($nivel==0){
              $nprecio = "costo";
          }else{
          	 $nprecio = "precio"."$nivel";
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
		
		
		public function leelinv($mysqli,$cat){
          //***lee todos los datos de una tabla, un registro o todos los registros, de acuerdo con el argumento $filtro ***/
 
            $sqlCommand = "SELECT codigo,nombre FROM productos WHERE grupo = $cat ORDER BY nombre";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE SELEC PROD. ".mysqli_error($mysqli));
//inicializacion de arreglo
				while($tempo=mysqli_fetch_array($query1)){
			 		$result[] = array('cod' => $tempo['codigo'],'desc' => $tempo['nombre']);
			 };
            /* liberar la serie de resultados */
                  mysqli_free_result($query1);
                  /* cerrar la conexion */
                  mysqli_close($mysqli);
            if($result){
              return $result;  
            }
            else {
                 die('no hay resultados para lista de inventarios ');
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
		
        	
	}/*** fin de la clase ***/
	

	
?>