/**
 * @author jmv
 */
;
function alta(){
	var formData =$("#altaprov").serialize();
	var dcred = document.getElementById('dcred').value;
	  if(validate()){
			$.post('php/altaprov.php',formData,null,"json")
			.done(function(data) {
	 	    	var resul= data.resul;
	 	    	var dialogo= document.getElementById('altaprov');
	 	    	dialogo.reset();
	 			dialogo.classList.remove('dialog-container--visible');
	 			location.reload(true);
	 			alert("registro exitoso");
	 	    						})
	 	    	.fail(function(xhr, textStatus, errorThrown ) {		
	 	    	document.write("ERROR EN REGISTRO:"+errorThrown);
	 			});
		}	
}

function validate(){
		if(razon===''){
			alert("Debe proporcionar una razon social");
			$("#razon").focus();
			return false;
		}
		if($("#rfc").val() === ''){
			alert("Debe Proporcionar un RFC válido");
			$("#nomcor").focus();
			return false;
		}
		if($("#nomcor").val() === ''){
			alert("Debe Proporcionar un nombre corto");
			$("#nomcor").focus();
			return false;
		}
		if($("#dcred").val() === ''){
			alert("Debe Proporcionar los días de crédito");
			$("#dcred").focus();
			return false;
		}
		return true;
}