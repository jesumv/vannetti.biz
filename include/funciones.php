<?php
	function nalmac($cliente,$noalmac){
		/*** este script contiene las reglas para * **/ 
	/*** la construccion del numero de almacen***/ 
		$longi = strlen($noalmac);
		
		if($longi ==1){
			$no = $cliente.'00'.$noalmac;

		}else{
			$no = $cliente.'0'.$noalmac;
		}
		return $no;
	}
	
	function decidesuc($cliente,$noalmac){
		/*** este regresa el numero de almacen matriz * **/ 
		/*** si no se proporciono $noalmac * **/ 
		if(empty($noalmac)){
			$no = $cliente.'000';
		}else{
			$no = $noalmac;
		}
		return $no;
	}

?>