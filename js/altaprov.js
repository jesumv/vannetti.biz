/**
 * @author jmv
 */
$(document)
.on("submit", "#altaprov", function(){
  if(validate()){
		$.post('php/altaprov.php', $(this).serialize(), function(data){
			if(data == -1){
				alert("error en el alta de proveedor");
			} else if (data == 0){
				alert("alta de proveedor OK");
				location.reload(); 	
			}
		});
	}
})
;

function validate(){
		if($("#razon").val() === ''){
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
		return true;
}