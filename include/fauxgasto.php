	<?php
	function movdiario($mysqli,$cuenta,$ref,$tipom,$monto,$fecha,$concep,$subcta=NULL,$arch=NULL){
	    //todos los gastos son facturar 1
	    //esta funcion realiza 1 movimiento contable en diario. $tipom determina
	    // tipo de movimiento
	    //preparacion de string arch
	    $resuld=0;
	    $archm;
	    if($arch!=NULL){
	        $archm= substr($arch,11);
	    }else{$archm = NULL;}
	    
	    if($tipom==0){
	        $colum="debe";
	    }else{
	        $colum="haber";
	    }
	    
	    if (!$mysqli->query("INSERT INTO diario(cuenta,subcuenta,referencia,$colum,fecha,facturar,coment,arch)
			VALUES($cuenta,'$subcta','$ref',$monto,'$fecha',1,'$concep','$archm')")){
			throw New Exception(" EN REG PRINC: $mysqli->error");
	    }else{return $resuld;}
	    
	    }
	    
	   function movaux($mysqli,$mpago,$cargo1,$ref,$concep,$montop,$efec,$ivaaux,$fecha,$subcta=NULL,$arch=NULL){
	        //registra los gastos auxiliares
	        //haber cuenta segun metodo de pago salvo si se eligio efectivo para la propina
	        $cargo;
	        $ivac;
	        $concepaux;
	        if($ivaaux==""){$ivac =0;}else{$ivac=$ivaaux;}
	        if($mpago=="03"){$concepaux="comision";}else{$concepaux="propina";}
	        $total = $montop+$ivac;
	        $cargo;
	        $abono;
	        $resula;
	        try{
	            //debe depende de si el gasto es alimentacion = propina no deducibles ventas
	            //en otro caso, es comision por transpaso = gasto financiero
	            //si no hay monto de movto, no se registra aux
	            if(is_numeric($montop)){
	            if($concep=="alim viaje"){$abono=602.83;}else{$abono=701.10;}
	            $resula=movdiario($mysqli,$abono,$ref,0,$montop,$fecha,$concepaux);
	            //o traspaso= gastos financieros + iva
	            if($ivaaux!=0){$resula=movdiario($mysqli,118.01,$ref,0,$ivac,$fecha,$concepaux);}
	            //definicion de metodo de pago
	            if($efec=="true"){$cargo = 101.01;}else{$cargo = ccargo($mpago);}
    	        $resula=movdiario($mysqli,$cargo,$ref,1,$total,$fecha,$concepaux); 
	            };
	        }catch(Exception $e){
	            $mal=$e->getMessage();
	            $resula=-2;}
	       return $resula;
	    }