/**
 * @author jmv
 * este script tiene las funciones auxiliares de la hoja oc.php
 */
'use strict';

function sumaarr(total,num){
	//suma los los elementos para un array
	return +total + + num;
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

function evaluaresp(datosresp){
	//evalua los codigos de respuesta y presenta el mensaje
	
}

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


function hazvisib(visible){
	var tabla = document.getElementById('ococult');
		    if (visible) {
		      tabla.classList.add('tablaocultav');
		    } else {
		      tabla.classList.remove('tablaocultav');;
		    }
};

function hazvisib2(visible){
	var tabla = document.getElementById('tpago');
		    if (visible) {
		      tabla.classList.add('tablaocultav');
		      
		    } else {
		      tabla.classList.remove('tablaocultav');
		    }
};

function addtot(){
	//añade los renglones de totales de la tabla
	// se define el elemento ancla
	var origen = document.getElementById("octabla");
	//se añade la fila de totales
	var idst;
	var textot;
	var texvar;
	var nombret1;
	var nombret2;
	var nombret11;
	var nombret12;
	var nombret21;
	var nombret22;
	var nodet;
	var nodet2;
	var nodet3;
	var tvari;
	var tvari2;
	var tvari3;
	var tvari4;
	//ciclo de renglones
	for(var r=0; r<3; r++){
		//ciclo de celdas	
			switch(r){
			case 0:
				textot = "SUBTOTALES";
				idst="tsubt";
				texvar="0.00"
				tvari="tprec";
				tvari2= "subtotalo";
				tvari3= "tcost";
				tvari4="tcant";
			break;
			case 1:
				textot = "IMPUESTOS";
				idst="timpt";
				texvar="---"
				tvari="timp";
				tvari2= "";
				tvari3= "";
				tvari4="";
				break;
			case 2:
				textot = "TOTAL";
				idst="ttot";
				texvar="---"
				tvari="ttotal";
				tvari2= "";
				break;		
			}
		for( var z=0; z<6; z++) {
			//seleccionar la clase adecuada
				var claset;
				var clase2;
				var idt;
				switch(z) {
				case 0:
			        claset = "ui-block-a";
			        idt=idst;
			        break;			     
			     case 1:
			        claset = "ui-block-b";
			        textot = "---";
			        idt="";
			    	 break;
			    	 
			     case 2:
				        claset = "ui-block-c";
				        textot = "---";
				        idt = tvari3;
				    	 break;
			     case 3:
				        claset = "ui-block-d";
				        textot = texvar;
				        idt = tvari4;
				    	 break;
			     case 4:
				        claset = "ocult";
				        textot = texvar;
				        idt = tvari2;
				    	break;
			     case 5:
				        claset = "ui-block-e";
				        textot= "0.00";
				        idt = tvari;
				    	break;
				}
			nombret1 = document.createElement("DIV");
			nombret2 = document.createElement("DIV");
			nombret1.className = claset;
			nombret2.className = "ui-bar ui-bar-a";
			nombret2.name = idt;
			nombret2.id = idt;
			nodet = document.createTextNode(textot);
			nombret1.appendChild(nombret2);
			nombret2.appendChild(nodet);
			origen.appendChild(nombret1);
		}
		
	}	
}

function validacant(cant){
	//convierte campos vacios en 0 para poder sumar
	var cantm;
	if (cant==""){cantm = 0}else{cantm=cant};
	return Number.parseInt(cantm);
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


function calcimps(rengl,preciot){
	//calcula impuestos del producto
	var iva = document.getElementById("iva"+rengl).innerHTML;
	var civa;
	var cieps;
	if(iva==="1"){
		civa= preciot*.16;
		document.getElementById("civa"+rengl).innerHTML=civa;
	}	
	var ieps = document.getElementById("ieps"+rengl).innerHTML;
	if(ieps==="1"){
		cieps= preciot*0.08;
		document.getElementById("cieps"+rengl).innerHTML=cieps;
	}

}

function ciclotag(clase){
	//recorre un ciclo de elementos por clase, suma los primeros hijos.
	var todos= document.getElementsByClassName(clase);
	var suma = 0;
	var inicial;
	for (var i=0; i<todos.length;i++){
			inicial = todos[i].firstChild.nodeValue;
			suma = suma + parseFloat(inicial);
	}
	return suma;
}
function sumaimp(){
	//suma los nodos de impuestos y regresa el total
	var totimps;
	var totiva=ciclotag("ivaf");
	var totieps=ciclotag("iepsf");
	totimps=totiva+totieps;
	return totimps;
}

function obtenreng(elemcosto){
	//extrae el no. de renglon
	var pos = elemcosto.indexOf("t");
	var rengl = elemcosto.slice(pos+1);
	return rengl;	
}
function ponsubt(){
	//se valida si la entrada es numerica
	 var checa = checaval(this.value);
	 var cad = this.name;
	 var rengl =obtenreng(cad);
	 if(checa == true) {
		 	//se registra el elemento que llamo. si fue precio, se modifica la casilla de precio cambiado
		 	var llamador = this.className === "cost"?1:0;
		 	if(llamador===1){document.getElementById("cambio"+ rengl).innerHTML="1"}
			//se toma el precio, se multiplica x cantidad y por present y se 
			//agrega el resultado a la tabla
			var longi = this.name.length;
			var precio = document.getElementById("cost"+ rengl).value;
			var valor = document.getElementById("cant"+ rengl).value;
			var preciot = precio*valor;
			document.getElementById("subtoc" +rengl).innerHTML =preciot;
			document.getElementById("subt"+ rengl).innerHTML = $.number(preciot,2);
			//calculo y escritura de impuestos
			calcimps(rengl,preciot);		
			// se modifican los totales
			var sumacants = sumacant();
			var sumaimps= sumaimp();
			document.getElementById("tcant").innerHTML = sumacants;
			var sumaprecs = sumaprecio();
			var sumapreciost= $.number(sumaprecs,2);
			document.getElementById("tprec").innerHTML = sumapreciost; 
			document.getElementById("subtotalo").innerHTML = sumaprecs.toFixed(2);
			document.getElementById("timp").innerHTML = sumaimps.toFixed(2);
			document.getElementById("ttotal").innerHTML = (sumaprecs+sumaimps).toFixed(2);
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
		 				var costo = redondea(obj1[z].costo,2);
		 				var iva= obj1[z].iva;
		 				var ieps= obj1[z].ieps;
		 				var reng = z;
		 //adicion de renglones de producto
		 				addprod(id,nombre,costo,reng,iva,ieps);
		 //adicion de escuchas en cajas input
		 				document.getElementById('cant'+z).addEventListener('input',ponsubt,false);
		 				document.getElementById('cost'+z).addEventListener('change',ponsubt,false);
		 		};
	//adicion de fila de totales
				addtot();
	//enfoque en el primer campo
				$('#cant0').focus();
	 		});
    	}

}

function addprod(id,nombre,costo,reng,iva,ieps){
	//adicion de celda inicial de id de renglon
	var nombre1 = document.createElement("DIV");
	nombre1.className = "ocult";
	nombre1.id = "ren"+reng;
	nombre1.name = "ren"+reng;
	var node = document.createTextNode(reng);
	nombre1.appendChild(node);
	var origen = document.getElementById("octabla");
	origen.appendChild(nombre1);
	
	var clase;
	var clase2;
	var texto;
	var elem;
	var idt;
	
	for( var z=0; z<11; z++) {
	//seleccionar la clase adecuada
		
		switch(z) {
	    case 0:
	    	idt = "id"+reng;
	        clase = "ui-block-a";
	        texto = id;	
	        break;
	    case 1:
	    	idt = "nom" + reng;
	    	clase = "ui-block-b";
	    	texto = nombre;
	        break;    
	    case 2:
	    	elem= "INPUT";
	    	idt = "cost" + reng;
	    	clase = "ui-block-c cant";
	    	clase2 = "cost";
	    	texto = costo;
	        break;	    	
	    case 3:
	    	elem = "INPUT";
	    	idt = "cant" + reng;
	    	clase = "ui-block-d cant";
	    	clase2 = "icant";
	    	texto = "";
	        break;
	    case 4:
	    	idt = "subtoc" + reng;
	    	clase = "ocult subtoc";
	    	texto = "";	
	        break; 
	    case 5:
	    	idt = "subt" + reng;
	    	clase = "ui-block-e prec";
	    	texto = "0.00";
	    	break;
	    case 6:
	    	idt = "iva" + reng;
	    	clase = "ocult ivac";
	    	texto = iva;
	        break;
	    case 7:
	    	idt = "ieps" + reng;
	    	clase = "ocult iepsc";
	    	texto = ieps;
	        break;
	    case 8:
	    	idt = "civa" + reng;
	    	clase = "ocult ivaf";
	    	texto = "0";
	        break;
	    case 9:
	    	idt = "cieps" + reng;
	    	clase = "ocult iepsf";
	    	texto = "0";
	        break;
	    case 10:
	    	idt = "cambio" + reng;
	    	clase = "ocult cambio";
	    	texto = "0";
	        break;
	    
	}
//definicion de elementos de acuerdo con la celda
		switch(z){
		//casos con un solo elemento, una clase
			case 0:
			case 1:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
				var nombre1 = document.createElement("DIV");
				nombre1.className = clase;
				nombre1.id = idt;
				nombre1.name = idt;
				var node = document.createTextNode(texto);
				nombre1.appendChild(node);
				var origen = document.getElementById("octabla");
				origen.appendChild(nombre1);
				break;
		//dos elementos 2 clases long max 10 valor 2 dec
			case 2:
			case 3:
				var nombre1 = document.createElement("DIV");
				var nombre2 = document.createElement(elem);
				nombre1.className = clase;
				var node = document.createTextNode(texto);
				nombre2.className = clase2;
				nombre2.type = "text";
				nombre2.size= "10";
				nombre2.maxlength="10";
				nombre2.name = idt;
				nombre2.id = idt;
				nombre2.value=texto;
				nombre2.appendChild(node);
				nombre1.appendChild(nombre2);
				var origen = document.getElementById("octabla");
				origen.appendChild(nombre1);
				break;
		//2 elementos 1 clase no se usa	
			default:
				var nombre1 = document.createElement("DIV");
				var nombre2 = document.createElement(elem);
				nombre1.className = clase;
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




