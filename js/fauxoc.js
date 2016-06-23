/**
 * @author jmv
 * este script tiene las funciones auxiliares de la hoja oc.html
 */

(function() {
//asignacion de variables
	llenaop();	
//escucha de seleccion de proveedor
	document.getElementById('ocprov').addEventListener('change', function() {
		//se muestra tabla
		hazvisib(true);
		haztabla();
	  }); 
})();

function llenaop(){
	  //esta funcion añade opciones a la lista de proveedores
		$.get('php/getprovs.php',function(data){
		var obj1 = JSON.parse(data);
		for( var z=0; z <obj1.length; z++) {
//extraccion de datos del array
			var id = obj1[z].id;
			var nombre = obj1[z].nombre;		
//adicion de opciones select
		  	var men = document.getElementById('ocprov');
			var option = document.createElement("option");
			option.text = nombre;
			option.value = id;
			men.add(option);
			};
		});	
};

function hazvisib(visible){
	var tabla = document.getElementById('octabla')
		    if (visible) {
		      tabla.classList.add('tablaocultav');
		    } else {
		      tabla.classList.remove('tablaocultav');
		    }
};

function haztabla(){
// Esta funcion construye la tabla de productos a elegir
	//obtener datos de los productos
		$.get('php/getprods.php',function(data){
 			var obj1 = JSON.parse(data);
 			for( var z=0; z <obj1.length; z++) {
 //extraccion de datos del array
 				var id = obj1[z].id;
 				var nombre = obj1[z].nombre;
 				var costo = obj1[z].costo;
 //adicion de celdas a la tabla de productos
 				addprod(id,nombre,costo);
 		};
 		});
}

function addprod(id,nombre,costo){
	for( var z=0; z<5; z++) {
	//seleccionar la clase adecuada
		var clase;
		var texto;
		var elem;
		switch(z) {
	    case 0:
	    	elem = "DIV"
	        clase = "ocult"
	        clase2 = " ocult"
	        texto = id	
	        break;
	    case 1:
	    	elem = "DIV"
	    	clase = "ui-block-a"
	    	clase2 = ""
	    	texto = nombre
	        break;
	    case 2:
	    	elem = "DIV"
	    	clase = "ocult"
	    	clase2 = " ocult"
	    	texto = costo
	        break;
	    case 3:
	    	elem = "INPUT"
	    	clase = "ui-block-b"
	    	clase2 = ""
	    	texto = ""
	        break;
	    default:
	    	elem = "DIV"
	    	clase = "ui-block-c"
	    	clase2 = ""
	    	texto = z*2
	}
		var nombre1 = document.createElement("DIV");
		var nombre2 = document.createElement(elem);
		nombre1.className = clase;
		nombre2.className = "ui-bar ui-bar-a"+ clase2;
		var node = document.createTextNode(texto);
		nombre1.appendChild(nombre2);
		nombre2.appendChild(node);
		var origen = document.getElementById("octabla");
		origen.appendChild(nombre1);
	};
};
