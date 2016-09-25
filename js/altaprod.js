/**
 * @author jmv
 */
$(document).on("submit","#altaprod", function(){
  if(validar()){
		$.post('php/altaprod.php', $(this).serialize(), function(data){
			if(data == -1){
				alert("error en movimiento de producto");
			} else if (data == 0){
				alert("alta de producto OK");
				location.reload(); 	
			}else if (data == 1){
				alert("cambio al producto OK");
				location.reload(); 	
			}
		});
	}
})


function validar(){
		if($("#selectmenu").val() === '0'){
			alert("Debe seleccionar un proveedor");
			$("#selectmenu").focus();
			return false;
		}
		if($("#selectmenu2").val() === '0'){
			alert("Debe seleccionar un grupo");
			$("#selectmenu2").focus();
			return false;
		}
		if($("#nombre").val() === ''){
			alert("Debe proporcionar un nombre");
			$("#nombre").focus();
			return false;
		}
		if($("#nomcor").val() === ''){
			alert("Debe proporcionar un nombre corto");
			$("#nomcor").focus();
			return false;
		}
		
		if($("#nomcat").val() === ''){
			alert("Debe proporcionar un nombre de catalogo");
			$("#nomcat").focus();
			return false;
		}
		
		if($("#cod").val() === ''){
			alert("Debe proporcionar un codigo de producto");
			$("#cod").focus();
			return false;
		}
		if($("#selectmenu3").val() === '0'){
			alert("Debe seleccionar una unidad");
			$("#selectmenu3").focus();
			return false;
		}
		if($("#cant").val() === ''){
			alert("Debe proporcionar la cantidad");
			$("#cant").focus();
			return false;
		}

		if($("#cost").val() === ''){
			alert("Debe proporcionar el costo del producto");
			$("#cost").focus();
			return false;
		}
		if($("#desc").val() === ''){
			alert("Debe proporcionar la descripción");
			$("#desc").focus();
			return false;
		}
		if($("#p1").val() === ''){
			alert("Debe Proporcionar al menos un precio de producto");
			$("#p1").focus();
			return false;
		}
		return true;
}