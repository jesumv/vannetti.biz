/**
 * @author jmv
 */
//instanciar arreglo para ordenes de compra
var noc =[];
(function() {
//llena la lista de proveedores
	llenaop();	
//escucha de seleccion de proveedor
	document.getElementById('locprov').addEventListener('change', function() {
//se desabilita la seleccion para prevenir segunda seleccion
		$('#locprov').selectmenu( "disable" );
//se hace visible la tabla de ocs y y arts y se llena
		dibuja();
	  });

})();

function aviso(texto){
	//esta funcion enciende el aviso de la pagina con el aviso
	//pasado como parametro.
	$("#aviso").html(texto);
	$("#aviso").popup("open");
}

function llenaop(){
	  //esta funcion añade opciones a la lista de proveedores
		$.get('php/getprovs.php',function(data){
		var obj1 = JSON.parse(data);
		for( var z=0; z <obj1.length; z++) {
//extraccion de datos del array
			var id = obj1[z].id;
			var nombre = obj1[z].nombre;		
//adicion de opciones select
		  	var men = document.getElementById('locprov');
			var option = document.createElement("option");
			option.text = nombre;
			option.value = id;
			men.add(option);
			};
		});	
};


function addlista(clave){
	//trae los datos de ordenes pendientes
	$.get('php/getlistoc.php',{idprov:clave},function(data){
		var lioc = JSON.parse(data);	
//se hace un renglon por cada orden por surtir. sólo si las hay para ese proveedor
		if(lioc!=0){
			var cadena;
			for(var z= 0; z <lioc.length; z++){
				//el número de oc 
				var nooc= lioc[z].id;
				//agregar al arreglo global
				noc.push(nooc);
				//construir cadena de texto
				cadena= "<a class='ui-btn' data-ajax='false' href='recoc.html?oc="+nooc+"'>ORDEN DE COMPRA "+nooc+"</a>";
				//construir los renglones
				$("#lista").append(cadena);
			};

		}else{
				aviso("NO HAY ORDENES DE  COMPRA PARA EL PROVEEDOR ELEGIDO")
				$("#locprov option[value='0']").attr('selected', 'selected');
				$('#locprov').selectmenu( "enable" )
				
		}
		
	});

	//agregar estilo mobile
	$("#pagina").enhanceWithin();
}




function dibuja(){
	//esta funcion trae los elementos para la tabla de oc 
	//y los anade al dom
	var prov = document.getElementById('locprov');
	var nprov = prov.value;
	//solo hay accion si se elige proveedor
	if(nprov!=0){
	//añadir ordenes de compra
		addlista(nprov);
	}
	
}
