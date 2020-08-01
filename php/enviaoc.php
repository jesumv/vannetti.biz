<?php
/*** este script registra el envio de una oc a proveedor***/ 
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
                        
                           
                    }
                    
                    $indip ++;  
                              
                }                  
                return 0;
        }
        
        function traeoc($mysqli){
            //funcion para traer el numero de la ultima oc
                $req = "SELECT MAX(idoc) FROM oc where 1";
                $result = mysqli_query($mysqli,$req);
                $row=mysqli_fetch_array($result,MYSQLI_NUM);
                if(mysqli_num_rows($result)>0){
                    return $row[0];
                }else{
                    throw new Exception("Error en consulta No. OC",5);               
                }
                /* liberar la serie de resultados */
                mysqli_free_result($result);
        }
        
    if (is_object($mysqli)) {
    	/*** checa login***/
    	$funcbase->checalogin($mysqli); 	
    try{//recoleccion de variables
    	$prov = $_POST["prov"];
    	$credito=$_POST["cred"];
    	$tpago=$_POST["tpago"];
    	$facturar=$_POST["fact"];
    	$cambiatp=$_POST["cambiatp"];
    	$cambiapi=$_POST["cambiapi"];
    	$cants=$_POST["cants"];
    	$prods=$_POST["prods"];
    	$preciou = $_POST["preciou"];
    	$preciot=$_POST["preciot"];
    	$imps=$_POST["imps"];
    	$stotal=$_POST["stotal"];
    	$total=$_POST["total"];
    	
    	//creacion de datos para oc
        $arts= array_sum($cants);
    	$usu= $_SESSION['usuario'];

    	//creacion de oc en tabla oc
    	$sqlCommand= "INSERT INTO oc (idproveedores,arts,imps,monto,total,saldo,usu,status,facturar,credito,tpago)
	    	VALUES ($prov,$arts,$imps,$stotal,$total,$total,'$usu',1,$facturar,$credito,'$tpago')";
    //commit manual de sql
    	mysqli_autocommit($mysqli, false);
    	$query=mysqli_query($mysqli,$sqlCommand);
    	if($query){
    	    //obtencion de numero de orden de compra
    	        $noc = traeoc($mysqli);
    	        $jsondata['resultado'] = 0;
    	        $jsondata['noc'] = $noc;
    	        $jsondata['arts'] = $arts;
    	        $jsondata['total'] =$total;
    	    //insercion de productos en tabla artsoc
    	        $indi = 0;
    	        foreach($prods as $id){
    	            $sqlCommand= "INSERT INTO artsoc (idoc,idproductos,cant,preciou,preciot,status)
		    			VALUES ($noc,$prods[$indi],$cants[$indi],$preciou[$indi],$preciot[$indi],1)";
    	            $query2=mysqli_query($mysqli, $sqlCommand);
    	            if($query2){
    	                $jsondata['noc'] = $noc;
    	                $jsondata['arts'] = $arts;
    	                $jsondata['total'] =$total;
    	                //se actualizan precios si así lo indica la bandera
    	                if($cambiatp==1){
                            $cambiop=cambiaprecios($mysqli,$prods,$cambiapi,$preciou);
                            $cambiop==0 ? $jsondata['cambiop'] = 0:"";
    	                }
    	                
    	            }else{
    	                $jsondata['errorsql']= mysqli_error($mysqli);
    	                throw new Exception("error en alta artsoc",3);
    	            }
    	            $indi++;
    	        }
    	     //se compromete la transacción.
    	        mysqli_commit($mysqli);
    	}else{ 
    	       $jsondata['errorsql']= mysqli_error($mysqli);
    	       throw new Exception("error en alta oc",2);
    	   }
    	
    }catch(Exception $ex){
        $jsondata['resultado']=$ex->getCode();
        $jsondata['mensaje']=$ex->getMessage(); 
        $jsondata['errorsql']= mysqli_error($mysqli);
    }finally{
        $mysqli->close();
        //fin de las transacciones//
    }
    }else{
        $jsondata['resultado'] = 11;
        $jsondata ['errort']= "error en conexion";
        $jsondata['errorsql'] =mysqli_connect_error();        
        }
        //salida de respuesta
        echo json_encode($jsondata);