<?php
/*** Autoload class files ***/ 
    function __autoload($class){
      require('include/' . strtolower($class) . '.class.php');
    }
	
	 $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
    	/*** checa login***/
    	$funcbase->checalogin($mysqli);	
	}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	
	  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
	       Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Vannetti Cucina</title>
	<meta name="author" content="jmv">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="img/logomin.gif" />  
	<link rel="apple-touch-icon" href="img/logomin.gif">
	<link rel="stylesheet" href= "css/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href= "css/movil.css" />
	<script src="js/jquery.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	<script src="js/jquery.number.js"></script>
	

	<script>
	'use strict';
	(function() {
		var facturarped;
		function llenactes(){
	//esta funcion añade opciones a la lista de clientes
			$.get('php/getctes.php',function(data){
			var obj1 = JSON.parse(data);
			for( var z=0; z <obj1.length; z++) {
	//extraccion de datos del array
				var id = obj1[z].id;
				var nombre = obj1[z].nombre;		
	//adicion de opciones select
			  	var men = document.getElementById('pcte');
				var option = document.createElement("option");
				option.text = nombre;
				option.value = id;
				men.add(option);
				};
			});	
		}
		
		function aviso(texto){
				//esta funcion enciende el aviso de la pagina con el aviso
				//pasado como parametro.
				$("#aviso").html(texto);
				$("#aviso").popup("open");
			}
			
		function hazvisib(visible){
			//esta funcion presenta la tabla de productos
		var tabla = document.getElementById('ocultable');
			    if (visible) {
			      tabla.classList.add('tablaocultav');
			    } else {
			      tabla.classList.remove('tablaocultav');
			    }
	};
	function checaval(valor){
		//esta funcion checa si el valor introducido es numerico o no esta en blanco
		if (isNaN(valor)&&valor!=''){
			return false;
		}else{
			if(valor <0){
			return false;	
			}else{return true;}
		}
	}
	
	function validacant(cant){
	//esta funcion convierte los blancos en 0s para los calculos
	var cantm;
	if (cant==""){cantm = 0;}else{cantm=cant;};
	return Number(cantm);
}
	
	function sumacant(){
		//esta funcion suma las cantidades de productos
	var cantt= 0;
	var arre = document.getElementsByClassName("cantp");
	var longit = arre.length;
	for(var z=0; z<longit; z++){
		var cantact = arre[z].value;
		var cantf = validacant(cantact);
		cantt = cantt + cantf;
	}
	return cantt;
}

function sumaprecio(){
	//esta funcion suma los precios
	var preciot =0;
	var arre = document.getElementsByClassName("stotoc") ;
	var longit = arre.length;
	for(var z=0; z<longit; z++){
		var preact = Number(arre[z].innerText);
		var preciot = preciot + preact;
	}
	return preciot;
}

function sumaiva(){
	//esta funcion suma los ivas
	var ivat=0;
	var arre = document.getElementsByClassName("ivaoc") ;
	var longit = arre.length;
	for(var z=0; z<longit; z++){
		var ivact = Number(arre[z].innerText);
		var ivat = ivat + ivact;
	}
	return ivat;
}

function sumatotas(){
	//esta funcion suma los montos totales de todos los renglones
	var grant=0;
	var arret= document.getElementsByClassName("totoc");
	var longit = arret.length;
	for(var z=0; z<longit; z++){
		var totact = Number(arret[z].innerText);
		var grant = grant+ totact;
	}
	return grant;
}

function sacareng(nombre){
			//esta funcion extrae el renglon de un nombre
			var longi = nombre.length;
			var remov = longi-3;
			var reng = nombre.slice(-remov);
			return reng;
		}

function quita(){
	  		//quitar cajas de peso
			 //var pesos = document.getElementById('dialpeso').getElementsByTagName("input");
			 //var x = pesos.length;
			 $("#pidepeso").popup("close");
			// for(var i=0; i<x; i++) {
			 	//pesos[0].parentNode.removeChild(pesos[0]);
			 //}
			 $('.divpeso').remove();
			 $('#regpeso').remove();
			 //limpiar titulo
			 document.getElementById("tpeso").innerHTML ="PESO ARTICULO(S) EN ";
	}
	
function ocosto(costo,peso){
		//esta funcion obtiene el costo de un articulo
		//en base a su peso
		var costoc= costo*peso;
		return costoc;	
		}
function getSum(total,num) {
	//funcion para sumar los valores de un array
    return total + num;
}			
function regpeso(reng){
// TODO revisar el producto salmon fresco s/p y salmon congelado segun peso
	//esta funcion registra en el html el costo de los articulos por peso
	var caspeso = document.getElementsByClassName("cajapeso");
	var nopesos = caspeso.length;
	var idart;
			var pesoact;
			var precioact;
			var costocalc;
			var costosum;
			var pesosum=[];
			//inicializacion de suma peso
			for(var i = 0;i < nopesos; i++){
				idart= document.getElementById("id"+reng).innerHTML;
				pesoact = parseFloat(document.getElementById("pesoac"+i).value);
				pesosum.push(pesoact);
			}
			var pesof=pesosum.reduce(getSum);
			document.getElementById("pesor"+ reng).innerHTML= pesof;
			precioact = document.getElementById("precio"+ reng).innerHTML;
			costocalc = ocosto(precioact,pesof)
			$("#pidepeso").popup("close");
			document.getElementById("psubt"+ reng).innerHTML= $.number(costocalc,2);	
			document.getElementById("stotoc"+ reng).innerHTML= Number(costocalc,2);	
			//calculo de datos renglon
	 		calculareng(reng)
	 		//calcula totales
	 		calculatots();		
}
	
function pidepeso(arts,reng){
		//esta funcion presenta el dialogo para anotar peso de productos
		//agregar las unidades de peso
		var titpeso=document.getElementById("tpeso").innerHTML;
		var tud= document.getElementById("ud"+reng).innerHTML;
		document.getElementById("tpeso").innerHTML=titpeso+tud+"?";
		//agregar cajas para registrar peso
		var origen = document.getElementById("dialpeso");
		var boton = document.createElement("INPUT");
		boton.type ="BUTTON";
		boton.value = "LISTO";
		boton.id = "regpeso"
		for(var i=0;i<arts;i++){
			var divi = document.createElement("DIV");
			divi.className="divpeso"
			var caja = document.createElement("INPUT");
			caja.id = "pesoac"+i;
			caja.className= "cajapeso";
			caja.placeholder = "articulo "+(i+1);
			origen.appendChild(divi);
			divi.appendChild(caja);
		}
		origen.appendChild(boton);
		var cancela = document.getElementById("cancpeso");
		cancela.addEventListener('click',function(){quita()},false);
		var registra = document.getElementById("regpeso");
		registra.addEventListener('click',function(){
		regpeso(reng);
		quita();
		},false);
		$("#pidepeso").popup("open");
		$("#pesoac0").focus();
		
}

function calculatots(){
	//esta funcion calcula los totales x columna y los escribe
			var sumacants = sumacant();
			document.getElementById("sumcantp").innerHTML = sumacants;
			var sumaprecs = sumaprecio();
			var sumapreciost= $.number(sumaprecs,2);
			document.getElementById("sumstotp").innerHTML = sumapreciost;
			document.getElementById("sumstotoc").innerHTML = sumaprecs;
			var sumatots= sumatotas();
			var sumatots2= $.number(sumatots,2);
			document.getElementById("sumtotp").innerHTML = sumatots2;
			document.getElementById("sumtotoc").innerHTML = sumatots;
			var sumaivas=sumaiva();
			var sumaivas2=$.number(sumaivas,2);
			document.getElementById("sumivap").innerHTML = sumaivas2;
			document.getElementById("sumivaoc").innerHTML = sumaivas;	
}

function quitadatos(rengl){
	//esta funcion quita los datos de un renglon cuando se borra la cantidad
	document.getElementById("psubt"+ rengl).innerHTML="0.00";
	document.getElementById("stotoc"+ rengl).innerHTML="0.00";
	document.getElementById("piva"+ rengl).innerHTML="0.00";
	document.getElementById("ivaoc"+ rengl).innerHTML="0.00";
	document.getElementById("ptot"+ rengl).innerHTML="0.00";
	document.getElementById("totoc"+ rengl).innerHTML="0.00";
	
}

function calculareng(rengl){
	//calcula datos de un renglon luego de modificacion
		 		//retomar el subtotal, para los calculos
	var subt= document.getElementById("stotoc"+ rengl).innerHTML;
	//si se va a facturar se busca iva por articulo
	 		if(facturarped==true){
	 			//si el articulo causa iva, se agrega a la columna
	 			var civa2 = document.getElementById("iva"+rengl).innerHTML;
	 			var iva1;
	 			var ivacalc;
	 				if(civa2==1){
			 			//si causa iva, se calcula
			 			var iva1= subt*.16;
						ivacalc=$.number((iva1),2);
	 				}else{
	 					ivacalc=$.number((0),2);
	 					iva1=0;
	 					}
	 		}else{
	 			ivacalc=$.number((0),2);
	 			iva1=0;
	 			}
	//se añade el iva
		document.getElementById("ivaoc"+rengl).innerHTML= iva1;
		document.getElementById("piva"+rengl).innerHTML= ivacalc;
	//se modifica el total del renglon
	 			var total=	parseFloat(subt)+parseFloat(ivacalc);
	 			document.getElementById("ptot"+rengl).innerHTML= $.number(total,2);	
	 			document.getElementById("totoc"+rengl).innerHTML= total;			
}

function multiplica(){
		//esta funcion obtiene el precio final en base a los datos de cantidad
		//se valida si la entrada es numerica
	 	var checa = checaval(this.value);
	 	var cad = this.name;
	 	var longi = this.name.length;
	    var pos = cad.indexOf("k");
		var rengl = cad.slice(pos+1);
		var precio = document.getElementById("precio"+ rengl).innerHTML;
		var valor = document.getElementById("chk"+ rengl).value;
		var multip;	
		var subt;
		var ivacalc;
		var total;
		//si la casilla tiene valor, se examina
	 	if (checa==true && valor!='') {
	 		//si el articulo se vende por peso, se muestra el combo
	 		var peso=document.getElementById("peso"+rengl).innerHTML;
	 			if(peso==1){
	 				multip=pidepeso(valor,rengl);
	 			}else{
	 				multip=valor;
				//se toma el precio oculto, se multiplica x cantidad y se añade a la tabla
					var preciot = ocosto(precio,multip);
					document.getElementById("psubt"+ rengl).innerHTML = $.number(preciot,2);
					document.getElementById("stotoc"+ rengl).innerHTML  = preciot;
				//calculo de datos renglon
	 				calculareng(rengl)
	 			// se modifican los totales
					calculatots();
	 			}

	 		
	 	}else{
	 		if(valor!=''){
	 		//el valor no es admisible
	 		aviso("debe introducir una cantidad positiva");
		 			$("#aviso").on( "popupafterclose", function( event, ui ) {
						var enfoc = document.getElementById(cad);
						enfoc.value = "";
						enfoc.focus();
	 				});
	 		}
		 	if(checa==true){
		 		//si la cantidad está en blanco, se quitan los datos del renglon
		 		quitadatos(rengl);
		 		calculatots();
		 	}	
			};
			

		}
		
		function addrengart(id,categ,nombre,precio,linea,iva,spesov,ud,presen){
			//esta funcion agrega los renglones de articulos para una categoría.
			var ancla = document.getElementById("cat"+categ);
		//celdas ocultas
		//id
		var idr = document.createElement("DIV");
			idr.className = "ocult idp";
			idr.id = "id"+linea;
			idr.name = "id"+linea;
			var noder = document.createTextNode(id);
			idr.appendChild(noder);
			ancla.appendChild(idr);
		//precio
			var precior = document.createElement("DIV");
			precior.className = "ocult prp";
			precior.id = "precio"+linea;
			precior.name = "precio"+linea;
			var node = document.createTextNode(precio);
			precior.appendChild(node);
			ancla.appendChild(precior);
		//causa iva
			var ivar = document.createElement("DIV");
			ivar.className = "ocult ivap";
			ivar.id = "iva"+linea;
			ivar.name = "iva"+linea;
			var node = document.createTextNode(iva);
			ivar.appendChild(node);
			ancla.appendChild(ivar);
		
		//venta por peso
			var spesor = document.createElement("DIV");
			spesor.className = "ocult spesov";
			spesor.id = "peso"+linea;
			spesor.name = "peso"+linea;
			var node = document.createTextNode(spesov);
			spesor.appendChild(node);
			ancla.appendChild(spesor);
		//unidad
			var udr = document.createElement("DIV");
			udr.className = "ocult";
			udr.id = "ud"+linea;
			udr.name = "ud"+linea;
			var node = document.createTextNode(ud);
			udr.appendChild(node);
			ancla.appendChild(udr);
		//presentacion
			var prer = document.createElement("DIV");
			prer.className = "ocult pres";
			prer.id = "prer"+linea;
			prer.name = "prer"+linea;
			var node = document.createTextNode(presen);
			prer.appendChild(node);
			ancla.appendChild(prer);
		//peso total de articulos
			var pesor = document.createElement("DIV");
			pesor.className = "ocult peso";
			pesor.id = "pesor"+linea;
			pesor.name = "pesor"+linea;
			var node = document.createTextNode("1");
			pesor.appendChild(node);
			ancla.appendChild(pesor);
			
			
		//columna 1
			var rengpa = document.createElement("DIV");
			rengpa.className = "ui-block-a";
			var checkp = document.createElement("INPUT");
			checkp.name = "chk"+linea;
			checkp.id = "chk"+linea;
			checkp.size="3";
			checkp.className = "cantp";
			rengpa.appendChild(checkp);
			ancla.appendChild(rengpa);
		//columna2	
			var rengpb = document.createElement("DIV");
			rengpb.className = "ui-block-b";
			rengpb.id = "pnom"+linea;
			var textopb = document.createTextNode(nombre);
			rengpb.appendChild(textopb);
			ancla.appendChild(rengpb);
		//columna3	
			var rengpc = document.createElement("DIV");
			rengpc.className = "ui-block-c subt";
			rengpc.id= "psubt"+linea;
			var textopc = document.createTextNode("0.00");
			rengpc.appendChild(textopc);
			ancla.appendChild(rengpc);
		//columna4
			var rengpd = document.createElement("DIV");
			rengpd.className = "ui-block-d subiva";
			rengpd.id= "piva"+linea;
			var textopd = document.createTextNode("0.00");
			rengpd.appendChild(textopd);
			ancla.appendChild(rengpd);
		//columna5
			var rengpe = document.createElement("DIV");
			rengpe.className = "ui-block-e gsubt";
			rengpe.id= "ptot"+linea;
			var textope = document.createTextNode("0.00");
			rengpe.appendChild(textope);
			ancla.appendChild(rengpe);
		//subtotal oculto sin formato
			var stotoc = document.createElement("DIV");
			stotoc.className = "ocult stotoc";
			stotoc.id = "stotoc"+linea;
			stotoc.name = "stotoc"+linea;
			var nodestot = document.createTextNode("0.00");
			stotoc.appendChild(nodestot);
			ancla.appendChild(stotoc);
		//iva sin formato
			var ivaoc = document.createElement("DIV");
			ivaoc.className = "ocult ivaoc";
			ivaoc.id = "ivaoc"+linea;
			ivaoc.name = "ivaoc"+linea;
			var nodeivao = document.createTextNode("0.00");
			ivaoc.appendChild(nodeivao);
			ancla.appendChild(ivaoc);
		//total oculto sin formato
			var totoc = document.createElement("DIV");
			totoc.className = "ocult totoc";
			totoc.id = "totoc"+linea;
			totoc.name = "totoc"+linea;
			var nodetot = document.createTextNode("0.00");
			totoc.appendChild(nodetot);
			ancla.appendChild(totoc);
			
		//agregar funcion de escucha
			document.getElementById(checkp.id).addEventListener('input', multiplica,false);	
		}
		
		function addarts(nocte){
			//esta funcion añade todos los artículos por categoría
			$.get('php/getlprodped.php',{idcte:nocte},function(data){
					var objarts = JSON.parse(data);
					var cantarts = objarts.length;
					for (var i=0;i<cantarts;i++){
						//obtener los valores
						var idprod = objarts[i].idprod;
						var grupo = objarts[i].gpo;
						var nombre = objarts[i].nombre;
						var precio =  objarts[i].precio;
						var iva = objarts[i].iva;
						var spesov = objarts[i].spesov;
						var ud= objarts[i].ud;
						var presen=objarts[i].presen;
						addrengart(idprod,grupo,nombre,precio,i,iva,spesov,ud,presen);
					}			
				});
		}
		function agregacab(linea){
	//esta funcion agrega los titulos del grid
			var envolt = document.createElement("DIV");
			envolt.className = "ui-grid-d";
			envolt.id = "cat"+linea;
			for(var i=0;i<5; i++){
				var ancla = document.getElementById("gr"+linea);
				var atr;
				var texto;
				switch(i){
					case 0:
						atr="ui-block-a ";
						texto="cantidad";
					break;
					case 1:
						atr="ui-block-b";
						texto="Producto";
					break;
					case 2:
						atr="ui-block-c";
						texto="monto";
					break;
					case 3:
						atr="ui-block-d";
						texto="iva";
					break;
					case 4:
						atr="ui-block-e";
						texto="total";
					break;
				} 
				var reng =document.createElement("DIV");
				reng.className= atr;
				var texto2= document.createTextNode(texto);
				var sreng = document.createElement("DIV");
				sreng.className = "ui-bar ui-bar-b";
				sreng.appendChild(texto2);
				reng.appendChild(sreng);
				envolt.appendChild(reng);
				ancla.appendChild(envolt);			
			}	
		};
		
		function agregatots(){
			//esta funcion agrega el renglon de totales
			var origen = document.getElementById("ptabla");
			var envolt = document.createElement("DIV");
			envolt.id = "totales";
			envolt.className = "ui-grid-d";
			var ancla = document.getElementById("totales");
			for(var i=0;i<8; i++){
					var atr;
					var rid;
					var texto;
					switch(i){
						case 0:
							atr="ui-block-a ";
							texto="0";
							rid="sumcantp";
						break;
						case 1:
							atr="ui-block-b";
							texto="TOTALES";
							rid="ttotales";
						break;
						case 2:
							atr="ui-block-c gtot";
							texto="0.00";
							rid="sumstotp";
						break;
						case 3:
							atr="ocult";
							texto="0.00";
							rid="sumstotoc";
						break;
						case 4:
							atr="ui-block-d gtot";
							texto="0.00";
							rid="sumivap";
						break;
						case 5:
							atr="ocult";
							texto="0.00";
							rid="sumivaoc";
						break;
						case 6:
							atr="ui-block-e gtot";
							texto="0.00";
							rid="sumtotp";
						break;
						case 7:
							atr="ocult";
							texto="0.00";
							rid="sumtotoc";
						break;
					} 
					var reng =document.createElement("DIV");
					reng.className= atr;
					var texto2= document.createTextNode(texto);
					var sreng = document.createElement("DIV");
					sreng.className = "ui-bar ui-bar-a";
					sreng.id = rid;
					sreng.appendChild(texto2);
					reng.appendChild(sreng);
					envolt.appendChild(reng);
					origen.appendChild(envolt);			
				}
		}
		function llenacats(){
	//esta funcion agrega las categorías de productos
			$.get('php/getcats.php',function(data){
				var obj1 = JSON.parse(data);
	//cabezales de categorias
				var origen = document.getElementById("ptabla");
				for( var z=0; z <obj1.length; z++) {
					//extraccion de datos del array
					var id = obj1[z].id;
					var nombre = obj1[z].nombre;
					var barra  = document.createElement("DIV");
					barra.name="gr"+id;
					barra.id = "gr"+id;
					barra.setAttribute("data-role","collapsible");
					barra.setAttribute("data-theme","a")
					barra.setAttribute("data-content-theme","a")
					var subtit  = document.createElement("H2");
					var texto = document.createTextNode(nombre);
					subtit.appendChild(texto);
					barra.appendChild(subtit);
					origen.appendChild(barra);
	//encabezados del grid
					agregacab(id);
				};
	//lista de articulos
		//	obtener el no. de cliente
					var numcte = document.getElementById("pcte").value;
					addarts(numcte);
	//renglon de totales
					agregatots();
					$("#ptabla").enhanceWithin();
			});
		}
		
		function validaelem(elem,valor){
		//esta funcion valida el elemento HTML que se pasa como argumento regresando 0 si el elemento
		//coincide o es nulo
		var resul;
		if(document.getElementById(elem)===null){resul = 0;}else{
			var texto = document.getElementById(elem).innerHTML;
			if(texto==valor){resul = 0;}else{resul = -1;}
		}
		return resul;
	}
		function validainput(elem,valor){
			var resul;
			//valida si el texto de un imput corresponde al argumento.
			if(document.getElementById(elem)===null){resul = 0;}else{
			var texto = document.getElementById(elem).value;
			if(texto==valor){resul = 0;}else{resul = -1;}
		}
		return resul;
		}
	
		function tipovta(){
			//esta funcion revisa que tipo de venta se registrará
			var radios = document.getElementsByName('rcredcon');

				for (var i = 0, length = radios.length; i < length; i++) {
				    if (radios[i].checked) {
				        // do whatever you want with the checked radio
				        return radios[i].value;
				        // only one radio can be logically checked, don't check the rest
				        break;
				    }
				}

		}
			
		function tipopedt(tipoped){
			var resul;
			switch(parseInt(tipoped)){
				case 0:
				resul = "MOSTRADOR";
				break;
				case 1:
				resul = "CONTADO";
				break;
				case 2:
				resul = "CREDITO";
				break;
				default:
				resul= "NO ESTA BIEN";
			}
			return resul;
		}   
		function evalua(resul,ped,tipoped){
			/** se evalua la respuesta del servidor**/
			switch (resul){
				case -99:
					var resp = "ERROR EN CONEXION A BD";
					break;
					
				case -1:
					resp = "ERROR EN REGISTRO PEDIDO";
					break;
				case -2:
					resp = "ERROR EN ALTA ARTS PEDIDO";
					break;
				case -3:
				resp = "ERROR EN INSERCIONES A DIARIO";
				break;
				default:
					var textof = tipopedt(tipoped);
					resp = "PEDIDO <br>Numero: "+ ped + "<br>"+"FORMA: "+textof+"<br>REGISTRO CORRECTO";
			}
			return resp;
		}
			
			function checadato(valor){
				//esta funcion checa si se introdujo un valor en una casilla
					var result = (valor!='') ? true:false;
					return result;
				}
			
			function llenaprod(){
				//llena un array con los datos de productos
				var prods=[];
				var cants=document.getElementsByClassName("cantp");
				var ids=document.getElementsByClassName("idp");
				var pus=document.getElementsByClassName("prp");
				var pres=document.getElementsByClassName("pres");
				var monts=document.getElementsByClassName("stotoc");
				var civas=document.getElementsByClassName("ivaoc");
				var pesos=document.getElementsByClassName("peso");
				var spesov=document.getElementsByClassName("spesov");
				var longi= cants.length;
				for(var i=0;i<longi;i++){
					var rengcant=cants[i];
					var rengid=ids[i];
					var rengpr=pus[i];
					var rengpres=pres[i];
					var rengmon=monts[i];
					var rengiva=civas[i];
					var rengpes=pesos[i];
					var rengspesov=spesov[i];
					var cantprod=rengcant.value;
					var idprod=rengid.innerHTML;
					var prprod=rengpr.innerHTML;
					var presp=rengpres.innerHTML;
					var montop=rengmon.innerHTML;
					var ivap=rengiva.innerHTML;
					var pesop=rengpes.innerHTML;
					var spesovp=rengspesov.innerHTML;
					var checact= checadato(cantprod);
					if(checact==true){
					//si hay cantidad, se registra el dato
						prods.push([idprod,cantprod,prprod,montop,ivap,presp,pesop,spesovp]);
					}
				}
				return prods;
			}
		$( document ).on( "pageinit", "#pedpag", function(event) {
			//fecha por defecto
	  		document.getElementById("fechav").valueAsDate = new Date();	
	  		//escucha del boton cancelar
			$("#pcancela").click(function(){
					document.getElementById("fechav").valueAsDate = new Date();
					//fecha por defecto	
			   		location.reload();
			   		$('#pcte').selectmenu( "enable" );
			   });
			   
			//modificacion de las etiquetas del flipswitch
			$( "#fact" ).flipswitch({
  				onText: "Si",
  				offText:"No"
			});
			//llenar el combo de clientes
			llenactes();			
	    	//agregar las categorías de productos
	    		document.getElementById('pcte').addEventListener('change', function() {	
	    			//decision de facturar
					facturarped = $("#fact").prop("checked");
					//desactivar flip facturar
					$( "#fact" ).flipswitch( "option", "disabled", true );
	    			//agregar categorias
	    			llenacats();
					//se muestra tabla
					hazvisib(true);
					//agregar estilo mobile
					$("#pedpag").enhanceWithin();
					//desenchufar select
					$('#pcte').selectmenu( "disable" );
				    //enfoque en primer elemento
	  				document.getElementById("fechav").focus();
		  	});	
		  		  	
	  		//escucha del boton enviar
	  		document.getElementById('penvia').addEventListener('click',function(){
	  			//identificacion de la primera linea
	  			var conjunto=document.getElementsByClassName("cantp");
	  			var primera=conjunto[0].id;
	  			var prods= llenaprod();
	  			//revisar que se hayan introducido datos
	  			var resul1 = validaelem("sumtotp","0.00");
	  			var fechav=document.getElementById("fechav").value
	  			var resul2=isValidDate(fechav)
	  			if(!resul2){
	  				aviso("POR FAVOR INTRODUZCA UNA FECHA VALIDA (AAAA-MM-DD)");
	  				$("#aviso").on( "popupafterclose", function( event, ui ) {
							document.getElementById("fechav").focus();
						} );
	  			}else{
		  			if(resul1==0){
		  				aviso("No ha solicitado ningun articulo:REVISE");
						$("#aviso").on( "popupafterclose", function( event, ui ) {
								document.getElementById(primera).focus();
							} );
		  			}else{
		  				//recolección de variables
		  					var cte = document.getElementById("pcte").value;
		  					var fecha = document.getElementById("fechav").value;
		  					var tventa = $("#tventa :radio:checked").val();
		  					var totarts =document.getElementById("sumcantp").innerHTML;
		  					var montot=document.getElementById("sumstotoc").innerHTML;
		  					var ivat=document.getElementById("sumivaoc").innerHTML;
		  					var total =document.getElementById("sumtotoc").innerHTML;
					//envio a bd
							$.post( "php/enviaped.php",
							{	facturarp:facturarped,
								prods:prods,
								cte:cte,
								fecha:fecha,
								tipoventa:tventa,
								totarts:totarts,
								montot:montot,
								totiva:ivat,
								total:total								
							 }, null, "json" )
	    						.done(function( data) {
	    							var resul=data.resul;
	    							var ped= data.ped;
	    							var tped= data.tventa;
	    							var cad = evalua(resul,ped,tped);
									aviso(cad);
									$( "#aviso" ).on( "popupafterclose", function( event, ui ) {
										location.reload();
										$('#pcte').selectmenu( "enable" );
									} );
	    						})
	    						.fail(function(xhr, textStatus, errorThrown ) {		
	    							document.write("ERROR EN ALTA DE PEDIDO:"+errorThrown);
								});	
		  									  			
		  			}
	  				
	  			}

	  			
	  		});	
		});
	})();
	//TODO hoja de envio de levantamiento y salida de pedido separadas-	
	</script>
	<script src="js/fauxcx.js"></script>
</head>

<body>
  <div data-role="page" id="pedpag"> 
	<div data-role="header">
    	<a href="portalmov.php" data-ajax="false" class="ui-btn-left ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-home">Inicio</a>
		<h1>Salida de Pedido</h1>
    	<a href="logout.php" data-ajax="false" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
	</div>

    <div role="main " class="ui-content">
			<a href="#navpanel" class="ui-btn ui-shadow ui-corner-all ui-btn-inline ui-btn-icon-left ui-icon-bars">Navegaci&oacute;n</a>
		<form  method="post"
		enctype="application/x-www-form-urlencoded" name="pforma" id="pforma">
		
		<div class="cajacent">
			<label for"fact">Facturar?</label>
	    	<input type="checkbox" data-role="flipswitch" id="fact" name="fact"/>
		</div>
			<div>
				<label for="pcte" class="select">Cliente</label>
				<select name="pcte" id="pcte">
			    	<option value="0">Seleccione al Cliente</option>
				</select>
			</div>
			<div id="ocultable" class="tablaoculta">
				<div class="cajacent">
					<label for ="fecha">Fecha:</label>
					 <input type="date" name="fechav" id="fechav">
				</div>
				<fieldset id="tventa" name="tventa" data-role="controlgroup" data-type="horizontal">
		    		<legend>Tipo de Venta</legend>
		    		<label>
		    			<input type="radio" id="rcredcon-a" name="rcredcon" value= "0" checked="checked"/>Mostrador
		    		</label>
		    		<label>
		    			<input type="radio" id="rcredcon-b" name="rcredcon" value= "1" />Contado
		    		</label>
		    		<label>
		    			<input type="radio" id="rcredcon-c" name="rcredcon" value= "2" />Credito
		    		</label>
    			</fieldset>	

				<div id="ptabla">
				</div>	
			     	<input data-theme="b" data-icon="check" data-iconshadow="true" value="Enviar" type="button" 
				    name="penvia"id="penvia">
				    <div class="ui-input-btn ui-btn ui-btn-b ui-icon-delete ui-btn-icon-left ui-shadow-icon" name="pcancela"id="pcancela">
				        Cancelar
				    	<input data-enhanced="true" value="Enhanced" type="button">
			    	</div>
			</div>
		</form>

    
		<div data-role="popup" id="aviso">
		<p>Sin texto, todavía.</p>
		</div>
		
		<div data-role ="popup" id="pidepeso" class="ui-content" data-dismissible="false" data-theme="b">
			<input type="button" data-theme="a" data-icon="delete" data-iconpos="notext" id="cancpeso"/>
			<label id="tpeso">PESO ARTICULO(S) EN </label>
			<div id="dialpeso">
				
			</div>	 
		</div>
  	</div>
  		<div data-role="panel" id="navpanel" data-display="overlay">
	 		<ul data-role ="listview">
	 			<li><a href="oc.php" data-ajax="false">Ordenes de Compra</a></li>
	 			<li><a href="listoc.php" data-ajax="false">Rec. de OC</a></li>
		    	<li><a href="pedido.php" data-ajax="false">Pedidos</a></li>
		    	<li><a href="mostrador.php" data-ajax="false">Vtas Mostrador</a></li>
		    	<li><a href="regmues.php" data-ajax="false">Muestras</a></li>
		    	<li><a href="listasp.php" data-ajax="false">Listas de Productos</a></li>
		    	<li><a href="portal.php" data-ajax="false">Portal</a></li>
	 		</ul>	    	
 		</div>
  </div>
</body>
</html>
