
<?php
 /*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    /*** funciones con bd ***/ 
    function movdiario($mysqli,$tipom,$cuenta,$monto,$fecha,$ref=NULL,$concep=NULL,$subcta=NULL){
        //esta funcion realiza 1 movimiento contable en diario. $tipom determina 
        if($tipom==0){
            $colum="debe";
        }else{
            $colum="haber";
        }
        try{
            $mysqli->autocommit(false);
            $mysqli->query("INSERT INTO diario(cuenta,$colum,fecha,facturar,subcuenta,referencia,coment)
			VALUES($cuenta,$monto,'$fecha',1,'$subcta','$ref','$concep')");
            //efectuar la operacion
            $mysqli->commit();
            $resul=0;
        }catch(Exception $e){
            //error en las operaciones de bd
            $mysqli->rollback();
            $resul=-2;
        }
        return $resul;
    }
    
    /*** variables ***/ 
    $funcbase = new dbutils;
	$resul;
	$jsondata;
	
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if(is_object($mysqli)){
        /*** checa login***/
        $funcbase->checalogin($mysqli);
        //creacion de arreglo de resultados
        $jsondata = array();
        //recolección de variables;
        $fecha=$_POST["fecha"];
        $ref=$_POST["ref"];
        $coment=$_POST["coment"];
        $datos = $_POST["cuentas"];
        //cuenta el no de elemento, para saber si es cuenta o monto
        $contar=1;
        //cuenta las columnas, para saber si es debe o haber
        $escribe= 0;
        $cuenta;
        $monto;
        $resul=0;
        foreach($datos as $value) {
            $creng=$contar/2;
            //definicion de clase de dato para insercion
            if(is_int($creng)){
                //es monto
                $monto=$value;
                $column=$escribe/2;
                if(is_int($column)){$columna=0;}else{$columna=1;};
                if($cuenta!=0){
                $resul1=movdiario($mysqli,$columna,$cuenta,$monto,$fecha,$ref,$coment);
                $resul=$resul+$resul1;
                }
                $escribe++;
            }else{
                //es cuenta
                $cuenta=$value;
            }
         $contar++; 
        }
	}else{$resul=-1;} 
	$jsondata['resul'] =$resul;
	echo json_encode($jsondata); 