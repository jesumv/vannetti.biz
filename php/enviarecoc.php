<?php
/***este script registra el envio, recepcion y pago en su caso, simultaneos de una oc a proveedor***/
/*** Autoload class files ***/ 

//funcion para el manejo de errores
function fallo($mensaje){
    throw new Exception($mensaje."NO SE EJECUTO MOVTO.");
}

    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    function traeoc($mysqli){
        //funcion para traer el numero de la ultima oc
        $req = "SELECT MAX(idoc) FROM oc where 1";
        $result = mysqli_query($mysqli,$req);
        $row=mysqli_fetch_array($result,MYSQLI_NUM);
        /* liberar la serie de resultados */
        mysqli_free_result($result);
        return $row[0];
    }
    
    function compra($mysqli,$monto,$refe,$credito,$tpago,$prov,$iva,$fact,$fechaconv,$subcta=null ){
        //esta funcion registra en el diario una compra a credito
        //normalizacion de iva a 0  si no se provee
        if(!$iva){$iva =0;}
        $cargo="115.01";
        $total = $monto+$iva;
        switch($credito){
            //contado
            case 0:
                $cargo2="118.01";
                switch($tpago){
                    //efectivo
                    case 1:
                        $cabono="101.01";                    
                        break;
                        // transferencia
                    case 3:
                        $cabono="102.01";
                        break;
                        // tdc
                    case 4:
                        $cabono="205.00";
                        break;
                        //tdebito
                    case 28:
                        $cabono="102.01";
                        break;
                        
                }

                //cargo
                $sqlmov1 ="INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)VALUES($cargo,'$refe',$monto,'$fechaconv',$fact )";
                $mysqli->query($sqlmov1)? null: fallo('cargoc1'.mysqli_error($mysqli)); 
                $sqlmov2 ="INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)VALUES($cargo2,'$refe',$iva,'$fechaconv',$fact)";
                $mysqli->query($sqlmov2)? null: fallo('cargoc2'.mysqli_error($mysqli)); 
                //abono
                $sqlmov3 ="INSERT INTO diario(cuenta, subcuenta,referencia,haber,fecha,facturar)VALUES($cabono,'$subcta','$refe',$total,'$fechaconv',$fact )";
                $mysqli->query($sqlmov3)? null: fallo('abonoc'.mysqli_error($mysqli)); 
                break;
                //credito
            default:
                $cargo2="119.01";
                $cabono="201.01";
                $sqlmov1 ="INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)VALUES($cargo,'$refe',$monto,'$fechaconv',$fact )";
                $mysqli->query($sqlmov1)? null: fallo('cargoa1'.mysqli_error($mysqli));
                $sqlmov2 ="INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)VALUES($cargo2,'$refe',$iva,'$fechaconv',$fact)";
                $mysqli->query($sqlmov2)? null: fallo('cargoa2'.mysqli_error($mysqli));
                //abono
                $sqlmov3 ="INSERT INTO diario(cuenta,subcuenta,referencia,haber,fecha,facturar)VALUES($cabono,'$prov','$refe',$total,'$fechaconv',$fact )";
                $mysqli->query($sqlmov3)? null: fallo('abonoa'.mysqli_error($mysqli));
                break;
        }
    }

      
    $funcbase = new dbutils;
    /*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
        /*** checa login***/
        $funcbase->checalogin($mysqli);
        /** se inicializa la repuesta como exito**/
        /** registro de la orden de compra *************************/
        //recoleccion de variables
        $fecha = $_POST["fecha"];
        $prov = $_POST["prov"];
        $cants=$_POST["cants"];
        $prods=$_POST["prods"];
        $preciou = $_POST["preciou"];
        $preciot=$_POST["preciot"];
        $total=$_POST["total"];
        $tpago=$_POST["tpago"];
        $credito=$_POST["cred"];
        $factura=$_POST["factura"];
        $subcta = $_POST["ctapago"];
        $facturar=$_POST["fact"];
        $jsondata = array();
        //creacion de datos para oc
        $arts= array_sum($cants);
        $usu= $_SESSION['usuario'];
        $ivat=0;

        $resultado=0; // our control variable 
        
        $mysqli->autocommit(false);
        try {
            //creacion de oc en tabla oc
            $sqlCommand= "INSERT INTO oc (idproveedores,arts,monto,total,saldo,fecharec,fechapago,usu,status,factura,facturar,credito,tpago)
	    	     VALUES ($prov,$arts,$total,$total,$total,'$fecha','$fecha','$usu',11,'$factura',$facturar,$credito,'$tpago')" ;
            $mysqli->query($sqlCommand)? null: fallo('alta oc '.mysqli_error($mysqli)); 
            //obtencion de numero de orden de compra
            $noc = traeoc($mysqli);
            $refe="oc".$noc;
            //insercion de productos en tabla artsoc
            $indi = 0;
            foreach($prods as $id){
                $sqlCommand2= "INSERT INTO artsoc (idoc,idproductos,cant,preciou,preciot,status)
		    			VALUES ($noc,$prods[$indi],$cants[$indi],$preciou[$indi],$preciot[$indi],2)";
                $mysqli->query($sqlCommand2) ? null : fallo('alta arts '.mysqli_error($mysqli)); 
                $umulti = $cants[$indi];
                //insercion en  inventario
                $sqlCommand3 = "INSERT INTO inventario (idproductos,tipomov,cant,usu,idoc,debe,factu,fechamov)
			    		VALUES ($prods[$indi],1,$cants[$indi],'$usu',$noc,$preciot[$indi],$facturar,'$fecha')";
                $mysqli->query($sqlCommand3) ? null : fallo('alta invs '.mysqli_error($mysqli));
                
                $indi++;
              
            }
            /** FIN DE EMISION OC *************************/
            
            /**recepcion de oc y arts *************************/
                //registro en diario
            compra($mysqli,$total,$refe,$credito,$tpago,$prov,$ivat,$facturar,$fecha,$subcta);
            $mysqli->commit();
            
        } catch (Exception $e) {
            $mysqli->rollback();
            $mysqli->close(); 
            $resultado = -1;
            die($e->getMessage());
        }
       
        
        //salida final
        $jsondata['noc'] = $noc;
        $jsondata['arts'] = $arts;
        $jsondata['total'] =$total;
        //fin de las transacciones

           
    }else{
        $resultado = "error en conexion bd";
    }
    
    // evaluacion del resultado
    $jsondata['success']= $resultado;
    
    //salida de respuesta
    echo json_encode($jsondata);
 