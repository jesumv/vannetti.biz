<?php
/***este script registra el envio, recepcion y pago en su caso, simultaneos de una oc a proveedor***/
/*** Autoload class files ***/ 
function __autoload($class){
    require('../include/' . strtolower($class) . '.class.php');
}
$funcbase = new dbutils;
/*** conexion a bd ***/
$mysqli = $funcbase->conecta();
$jsondata = array();


function cambiaprecios($mysqli,$idprods,$cambios,$preciosc){
    //actualiza los precios de productos con los datos enviados
    $indip=0;
    foreach($idprods as $id){
        if($cambios[$indip]==1){
            $consul="UPDATE productos set costo = $preciosc[$indip],costov =$preciosc[$indip]
            where idproductos= $idprods[$indip]";
            $queryp=mysqli_query($mysqli,$consul);
            if(!$queryp){
                $jsondata['errorsql']= mysqli_error($mysqli);
                throw new Exception("Error en cambio precios",4);
            }          
            $indip ++;
        }      
    }    
    return 0;
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
    
    function compra($mysqli,$stotal,$imps,$total,$refe,$credito,$tpago,$prov,$totiva,$totieps,
        $fact,$fechaconv,$subcta=null ){
        //esta funcion registra en el diario una compra 
        //normalizacion de iva a 0  si no se provee
        $jsondata = array();
        if(!$imps){$imps =0;}
        $total=$stotal+$imps;
        //inventarios
        $cargo="115.01";
        switch($credito){
            //contado
            case 0:
                //iva pagado
                $cargo2="118.01";
                //ieps pagado
                $cargo3="118.03";
                
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
                        $cabono="205.06";
                        break;
                        //tdebito
                    case 28:
                        $cabono="102.01";
                        break;
                        
                }
                    //abono a inventarios                   
                    $sqlmov1 ="INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)
                    VALUES($cargo,'$refe',$stotal,'$fechaconv',$fact )";                
                    if(!$resul1=$mysqli->query($sqlmov1)){
                        $jsondata['errorsql']= mysqli_error($mysqli);
                        throw new Exception("error en registro diario cargo inventarios",11);                   
                    }
                    //si hay iva abono a iva pagado
                    if($totiva>0){
                         $sqlmoviva ="INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)
                         VALUES($cargo2,'$refe',$totiva,'$fechaconv',$fact)";
                            if(!$resul2=$mysqli->query($sqlmoviva)){
                                $jsondata['errorsql']= mysqli_error($mysqli);
                                throw new Exception("error en registro diario iva pagado",21); 
                            }
                    }
                     //si hay ieps aboono a ieps pagado
                            if($totieps>0){
                                $sqlmovieps ="INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)
                                VALUES($cargo3,'$refe',$totieps,'$fechaconv',$fact)";
                                if(!$resulieps=$mysqli->query($sqlmovieps)){
                                    $jsondata['errorsql']= mysqli_error($mysqli);
                                    throw new Exception("error en registro diario ieps pagado",22);
                                }
                            }
                            //cargo a cta de pago
                            $sqlmov3 ="INSERT INTO diario(cuenta, subcuenta,referencia,haber,fecha,
                            facturar)VALUES($cabono,'$subcta','$refe',$total,'$fechaconv',$fact )";
                            if(!$resul3=$mysqli->query($sqlmov3)){
                                $jsondata['errorsql']= mysqli_error($mysqli);
                                throw new Exception("error en registro diario abono 1",13); 
                            }               
                    break;
                    //credito
            default:
                //iva por pagar
                $cargo2="119.01";
                //ieps por pagar
                $cargo3="119.03";
                //proveedores
                $cabono="201.01";
                //inventario
                $sqlmov1 ="INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)
                VALUES($cargo,'$refe',$stotal,'$fechaconv',$fact )";
                if(!$resul1=$mysqli->query($sqlmov1)){
                    $jsondata['errorsql']= mysqli_error($mysqli);
                    throw new Exception("error en registro diario abono inventario",14);
                } 
                //iva por pagar
              if($totiva>0){
                $sqlmov2 ="INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)
                     VALUES($cargo2,'$refe',$totiva,'$fechaconv',$fact)";
                if(!$resul2=$mysqli->query($sqlmov2)){
                    $jsondata['errorsql']= mysqli_error($mysqli);
                    throw new Exception("error en registro diario abono iva x pagar",15);
                } 
               }
                //ieps por pagar
               if($totieps>0){
                $sqlmov3 ="INSERT INTO diario(cuenta,referencia,debe,fecha,facturar)
                     VALUES($cargo3,'$refe',$totieps,'$fechaconv',$fact)";
                if(!$resul3=$mysqli->query($sqlmov3)){
                    $jsondata['errorsql']= mysqli_error($mysqli);
                    throw new Exception("error en registro diario abono ieps x pagar",15);
                }
               }
                //abono proveedores
                        $sqlmov4="INSERT INTO diario(cuenta,subcuenta,referencia,haber,fecha,facturar)
                        VALUES($cabono,'$prov','$refe',$total,'$fechaconv',$fact )";
                        if(!$resul4=$mysqli->query($sqlmov4)){
                            $jsondata['errorsql']= mysqli_error($mysqli);
                           throw new Exception("error en registro diario abono provs",16);
                        }
                break;
            }  
            $resul=0;                      
            return $resul;               
    }
// fin de compra         

        if(is_object($mysqli)){
            /*** checa login***/
            $funcbase->checalogin($mysqli);
         try{  
            /** registro de la orden de compra *************************/
            //recoleccion de variables
            $fecha = $_POST["fecha"];
            $prov = $_POST["prov"];
            $stotal=$_POST["stotal"];
            $imps = $_POST["imps"];
            $total=$_POST["total"];
            $tpago=$_POST["tpago"];
            $credito=$_POST["cred"];
            $cambiatp=$_POST["cambiatp"];
            $factura=$_POST["factura"];
            $subcta = $_POST["ctapago"];
            $facturar=$_POST["fact"];
            $totiva= $_POST["totiva"];
            $totieps= $_POST["totieps"];
            $cants=$_POST["cants"];
            $prods=$_POST["prods"];
            $preciou = $_POST["preciou"];
            $preciot=$_POST["preciot"];
            $cambiapi=$_POST["cambiapi"];
            // se revisa si debe haber fecha de pago
            $status;
            //si contado
            if($credito==0){$fechapag=$fecha;$saldoact=0;$status=99;}else{
                $fechapag = NULL; $saldoact=$total;$status=11;}
                //creacion de datos para oc
                $arts= array_sum($cants);
                $usu= $_SESSION['usuario'];
            //creacion de oc en tabla oc
            $sqlCommand= "INSERT INTO oc (idproveedores,arts,monto,imps,total,saldo,fecharec,fechapago,usu,status,factura,facturar,credito,tpago)
    	     VALUES ($prov,$arts,$stotal,$imps,$total,$saldoact,'$fecha','$fechapag','$usu',$status,'$factura',$facturar,$credito,$tpago)" ;
            //commit manual
            $mysqli->autocommit(false);
            $resp1=$mysqli->query($sqlCommand);
            if($resp1){
                //obtencion de numero de orden de compra
                $noc = traeoc($mysqli);
                $refe="oc".$noc;
                //insercion de productos en tabla artsoc
                $indi = 0;
                //3
                foreach($prods as $id){
                    //insercion de productos en tabla artsoc
                    $sqlCommand2= "INSERT INTO artsoc (idoc,idproductos,cant,preciou,preciot,status)
		    			VALUES ($noc,$prods[$indi],$cants[$indi],$preciou[$indi],$preciot[$indi],2)";
                    $query2=$mysqli->query($sqlCommand2);
                    if($query2){
                        //insercion en  inventario
                        $sqlCommand3 = "INSERT INTO inventario (idproductos,tipomov,cant,usu,idoc,debe,factu,fechamov)
			    		VALUES ($prods[$indi],1,$cants[$indi],'$usu',$noc,$preciot[$indi],$facturar,'$fecha')";
                        $query3=$mysqli->query($sqlCommand3) ;
                        if($query3){
                            //**recepcion de oc y arts *************************/
                            //registro en diario
                            $resul2=compra($mysqli,$stotal,$imps,$total,$refe,$credito,$tpago,$prov,$totiva,$totieps,$facturar,$fecha,$subcta); 
                            if($resul2!=0){throw new Exception("error en movs diario",5);};
                            //se actualizan precios si así lo indica la bandera
                            if($cambiatp==1){
                                $cambiop=cambiaprecios($mysqli,$prods,$cambiapi,$preciou);
                                $cambiop==0 ?$jsondata['cambiop'] = 0:"";
                            }
                            //se completa ciclo
                            $mysqli->commit();
                        }else{
                            $jsondata['errorsql']= mysqli_error($mysqli);
                            throw new Exception("error en alta inventario",4);
                        }
                    }else{
                        $jsondata['errorsql']= mysqli_error($mysqli);
                        throw new Exception("error en alta arts",3);
                    }
                    $indi++;
                }
                //salida final
                $jsondata['resultado']=0;
                $jsondata['noc'] = $noc;
                $jsondata['arts'] = $arts;
                $jsondata['total'] =$total;
            }else{throw new Exception("error en alta oc",2);}
        
    }catch(Exception $ex){
        $jsondata['resultado']=$ex->getCode();
        $jsondata['mensaje']=$ex->getMessage();
        $jsondata['errorsql']= mysqli_error($mysqli);
        $mysqli->rollback();
    }finally{
        //salida de respuesta
        $mysqli->close(); 
        //fin de las transacciones//
        echo json_encode($jsondata);
    }
    
        }else{throw new Exception("error de conexion 2",1);}
        
    

