/**
 * @author jmv
 * este script tiene las funciones auxiliares de la hoja oc.php
 */
(function() {
//llena la lista de proveedores
	llenaop();	
//escucha de seleccion de proveedor
	document.getElementById('ocprov').addEventListener('change', function() {
		//se muestra tabla
		hazvisib(true);
		haztabla();
		//desenchufar select
		$('#ocprov').selectmenu( "disable" );
	  });

})();

function aviso(texto){
	//esta funcion enciende el aviso de la pagina con el aviso
	//pasado como parametro.
	$("#aviso").html(texto);
	$("#aviso").popup("open");
}

function confirma(texto,oc){
	//esta funcion enciende aviso de conclusion de oc
	//con el texto pasado como parametro
	$("#datos").html(texto);
	$("#noc").val(oc);
	$("#confirma").popup("open");
}

function validaelem(elem,valor){
	//esta funcion valida el elemento que se pasa como argumento regresando 0 si el elemento
	//coincide o es nulo
	if(document.getElementById(elem)=== null){resul = -1}else{
		var texto = document.getElementById(elem).innerHTML;
		if(texto==valor){var resul = 0}else{resul = -1}
	}
	return resul
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
		  	var men = document.getElementById('ocprov');
			var option = document.createElement("option");
			option.text = nombre;
			option.value = id;
			men.add(option);
			};
		});	
};

function hazvisib(visible){
	var tabla = document.getElementById('ococult');
		    if (visible) {
		      tabla.classList.add('tablaocultav');
		    } else {
		      tabla.classList.remove('tablaocultav');;
		    }
};

function addtot(){
	
	//se añade la fila de totales
	for( var z=0; z<4; z++) {
		//seleccionar la clase adecuada
			var claset;
			var clase2;
			var textot;
			var idt;
			switch(z) {
			case 0:
		        claset = "ui-block-a";
		        textot = "TOTALES";
		        idt = "tsubt";
		        break;
		     case 1:
		        claset = "ui-block-b";
		        textot = "0";
		        idt = "tcant";
		    	 break;
		     case 3:
			        claset = "ocult";
			        textot = "0";
			        idt = "subtotalo";
			    	break;
		    
		     default:
		    	claset = "ui-block-c";
		        textot = "0.00";
		        idt = "tprec";
			}
		var nombret1 = document.createElement("DIV");
		var nombret2 = document.createElement("DIV");
		nombret1.className = claset;
		nombret2.className = "ui-bar ui-bar-a";
		nombret2.name = idt;
		nombret2.id = idt;
		var nodet = document.createTextNode(textot);
		nombret1.appendChild(nombret2);
		nombret2.appendChild(nodet);
		var origen = document.getElementById("octabla");
		origen.appendChild(nombret1);
	}
}

function validacant(cant){
	var cantm;
	if (cant==""){cantm = 0}else{cantm=cant};
	return Number(cantm);
}

function sumaprecio(){
	var preciot =0;
	var arre = document.getElementsByClassName("subtoc") 
	var longit = arre.length;
	for(var z=0; z<longit; z++){
		var preact = Number(arre[z].innerText);
		var preciot = preciot + preact;
	}
	return preciot;
}

function sumacant(){
	var cantt= 0;
	var arre = document.getElementsByClassName("icant");
	var longit = (arre.length);
	for(var z=0; z<longit; z++){
		var cantact = arre[z].value;
		var cantf = validacant(cantact);
		cantt = cantt + cantf;
	}
	return cantt;
}

function checaval(valor){
	//esta funcion checa si el valor introducido es numerico o no esta en blanco
	if (isNaN(valor)&&valor!=''){
		return false;
	}else{
		if(valor <0){
		return false;	
		}else{return true}
	}
}
function ponsubt(){
	//se valida si la entrada es numerica
	 var checa = checaval(this.value);
	 var cad = this.name
	 if(checa == true) {
			//se toma el precio oculto, se multiplica x cantidad y se 
			//agrega el resultado a la tabla
			var longi = this.name.length;
			var pos = cad.indexOf("t");
			var rengl = cad.slice(pos+1);
			var precio = document.getElementById("costo"+ rengl).innerHTML;
			var valor = document.getElementById("cant"+ rengl).value;
			var preciot = precio*valor;
			document.getElementById("subtoc" +rengl).innerHTML =preciot;
			document.getElementById("subt"+ rengl).innerHTML = $.number(preciot,2);
			
			// se modifican los totales
			var sumacants = sumacant();
			document.getElementById("tcant").innerHTML = sumacants;
			var sumaprecs = sumaprecio();
			var sumapreciost= $.number(sumaprecs,2);
			document.getElementById("tprec").innerHTML = sumapreciost; 
			document.getElementById("subtotalo").innerHTML = sumaprecs.toFixed(2);
	 }else{
		 aviso("debe introducir una cantidad positiva");
		$( "#aviso" ).on( "popupafterclose", function( event, ui ) {
				var enfoc = document.getElementById(cad);
				enfoc.value = "";
				enfoc.focus();
			} );
	 }

};

function haztabla(){
// Esta funcion construye la tabla de productos a elegir
	//obtener datos de los productos
	var select = document.getElementById('ocprov');
    var selected = select.options[select.selectedIndex];
    var key = selected.value;
    	if(key!=0){
    		//solo si se elige proveedor se envia por los datos
    		//examina el proveedor elegido
			$.get('php/getprods.php',{
				idprov: key
			},function(data){
	 			var obj1 = JSON.parse(data);
		 			for( var z=0; z <obj1.length; z++) {
		 //extraccion de datos del array
		 				var id = obj1[z].id;
		 				var nombre = obj1[z].nombre;
		 				var costo = obj1[z].costo;
		 				var reng = z;
		 //adicion de renglones de producto
		 				addprod(id,nombre,costo,reng);
		 //adicion de escuchas en cajas input
		 				document.getElementById('cant'+z).addEventListener('input',ponsubt,false);
		 		};
	//adicion de fila de totales
				addtot();
	//adicion de botones de accion	
	//enfoque en el primer campo
				$('#cant0').focus();
	 		});
    	}

}

function addprod(id,nombre,costo,reng){
	//adicion de celda inicial de id de renglon
	var nombre1 = document.createElement("DIV");
	nombre1.className = "ocult";
	nombre1.id = "ren"+reng;
	nombre1.name = "ren"+reng;
	var node = document.createTextNode(reng);
	nombre1.appendChild(node);
	var origen = document.getElementById("octabla");
	origen.appendChild(nombre1);
	
	for( var z=0; z<6; z++) {
	//seleccionar la clase adecuada
		var clase;
		var clase2;
		var texto;
		var elem;
		var idt;
		switch(z) {
	    case 0:
	    	elem = "DIV"
	    	idt = "id"+reng;
	        clase = "ocult"
	        texto = id	
	        break;
	    case 1:
	    	elem = "DIV"
	    	idt = "nom" + reng;
	    	clase = "ui-block-a"
	    	clase2 = ""
	    	texto = nombre
	        break;
	    case 2:
	    	elem = "DIV"
	    	idt = "costo" + reng
	    	clase = "ocult"
	    	texto = costo
	        break;
	    case 3:
	    	elem = "INPUT";
	    	idt = "cant" + reng
	    	clase = "ui-block-b cant"
	    	clase2 = "icant"
	    	texto = ""
	        break;
	    case 5:
	    	elem = "DIV"
	    	idt = "subtoc" + reng
	    	clase = "ocult subtoc"
	    	texto = ""
	        break;
	    	
	    default:
	    	elem = "DIV"
	    	idt = "subt" + reng
	    	clase = "ui-block-c prec"
	    	clase2 = ""
	    	texto = "0.00"
	}
//definicion de elementos de acuerdo con la celda
		switch(z){
			case 0:
			case 2,5:
				var nombre1 = document.createElement("DIV");
				nombre1.className = clase;
				nombre1.id = idt;
				nombre1.name = idt;
				var node = document.createTextNode(texto);
				nombre1.appendChild(node);
				var origen = document.getElementById("octabla");
				origen.appendChild(nombre1);
				break;
			case 3:
				var nombre1 = document.createElement("DIV");
				var nombre2 = document.createElement(elem);
				nombre1.className = clase;
				nombre2.className = clase2;
				nombre2.type = "text";
				nombre2.size= "3";
				nombre2.maxlength="3";
				nombre2.name = idt;
				nombre2.id = idt;
				var node = document.createTextNode(texto);
				nombre1.appendChild(nombre2);
				nombre2.appendChild(node);
				var origen = document.getElementById("octabla");
				origen.appendChild(nombre1);
				break;
			default:
				var nombre1 = document.createElement("DIV");
				var nombre2 = document.createElement(elem);
				nombre1.className = clase;
				nombre2.className = clase2;
				nombre2.name = idt;
				nombre2.id = idt;
				var node = document.createTextNode(texto);
				nombre1.appendChild(nombre2);
				nombre2.appendChild(node);
				var origen = document.getElementById("octabla");
				origen.appendChild(nombre1);	
		}
		
	};
};




