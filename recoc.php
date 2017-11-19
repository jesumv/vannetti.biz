<?php
  function __autoload($class){
	  require('include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
/*** checa login***/
       $funcbase->checalogin($mysqli);
    } else {
        //die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
    
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
	  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
	       Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Recepción OC</title>
	<meta name="author" content="jmv">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="img/logomin.gif" />  
	<link rel="apple-touch-icon" href="img/logomin.gif">
	<link rel="stylesheet" href= "css/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href= "css/movil.css" />
	<script src="js/jquery.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	<script src="js/fauxcx.js"></script>
	<script>
	'use strict';
	(function() {
		var cantreng;
		var fechar;
		var para;
		var remi;
		var fact;
		var ivat;
		var mtot;
			
		function getpar(name){
		//esta funcion obtiene el numero de orden de compra del string GET
	   		if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
	      	return decodeURIComponent(name[1]);
		}
		
		function aviso(texto){
			//esta funcion enciende el aviso de la pagina con el texto
			//pasado como parametro.
			$("#aviso").html(texto);
			$("#aviso").popup("open");
		}
		
		function addreng(nombre,cant,idprod,reng,speso,costo,civa,cud){
			//esta funcion añade un renglon de la tabla de articulos
			var origen = document.getElementById("tbrecoc");
			var nombre1 = document.createElement("DIV");
			//el numero de renglon
			nombre1.className = "ocult";
			nombre1.id = "ren"+reng;
			nombre1.name = "ren"+reng;
			var node = document.createTextNode(reng);
			nombre1.appendChild(node);
			origen.appendChild(nombre1);
				var clase;
				var clase2;
				var texto;
				var elem;
				var idt;
				var nombre1;
				var nombre2;
				var node;
				var tipo;
				
			for(var z=0;z<11;z++){
				nombre1 = document.createElement("DIV");
				switch(z){
					 case 0:
				    	elem = "DIV";
				    	idt = "id"+reng;
				        clase = "ocult";
				        clase2="";
				        texto = idprod;	
				        break;
				    case 1:
				    	elem = "DIV"
				    	idt = "nom" + reng;
				    	clase = "ui-block-a";
				    	clase2 = "";
				    	texto = nombre;
				        break;
				     case 2:
				    	elem = "DIV"
				    	idt = "cost" + reng;
				    	clase = "ocult";
				    	clase2 = "";
				    	texto = costo;
				        break;
				           
				    case 3:
				    	elem = "INPUT";
				    	idt = "cant" + reng;
				    	clase = "ui-block-b";
				    	clase2="icant";
				    	tipo = "TEXT";
				    	texto = cant;
				        break;
				    case 4:
				    	elem = "INPUT";
				    	tipo = "checkbox";
				    	idt = "chk" + reng;
				    	clase = "ui-block-c";
				    	clase2="ichk";
			        break;
			        
			        case 5:
			    	elem ="DIV";
			    	idt = "costof" + reng;
			    	clase =  "ui-block-d";
			    	clase2 ="";
			    	texto = "0.00";
			        break;	  
				        
				    case 6:
				    	elem ="DIV";
				    	idt = "speso" + reng;
				    	clase = "ocult";
				    	clase2 = "speso";
				    	texto = speso;
				        break;
				        
			        case 7:
			    	elem ="DIV";
			    	idt = "iva" + reng;
			    	clase = "ui-block-e";
			    	clase2 = "";
			    	texto = "0.00";
			        break;
			        
			        case 8:
			    	elem ="DIV";
			    	idt = "civa" + reng;
			    	clase = "ocult";
			    	clase2 = "";
			    	texto = civa;
			        break;
			        
			        case 9:
			    	elem ="DIV";
			    	idt="cud"+reng;
			    	clase = "ocult";
			    	clase2 = "";
			    	texto = cud;
			        break;
			        case 10:
			        elem ="DIV";
			    	idt="cpeso"+reng;
			    	clase = "ocult";
			    	clase2 = "pesos";
			    	texto = 0;
			        break;
			        			 	    
				}
	//adicion de elementos al DOM					
						nombre2 = document.createElement(elem);
						nombre1.className = clase;
						nombre2.className = clase2;
						nombre2.name = idt;
						nombre2.id = idt;
						if(tipo!= null){
							nombre2.type = tipo;
							if(tipo!="CHECKBOX"){
								nombre2.value = cant;
								nombre2.size= "3";
								nombre2.maxlength="3";
							}
						}
						node = document.createTextNode(texto);
						nombre2.appendChild(node);
						nombre1.appendChild(nombre2);
						origen.appendChild(nombre1); 
						$("#fecha").focus();	
			}
		}
		
		
		function quita(){
	  		//quitar cajas de peso
			 var pesos = document.getElementById('dialpeso').getElementsByTagName("input");
			 var x = pesos.length;
			 for(var i=0; i<x; i++) {
			 	pesos[0].parentNode.removeChild(pesos[0]);
			 }
			 $("#pidepeso").popup("close");
			 //limpiar titulo
			 document.getElementById("tpeso").innerHTML ="PESO ARTICULO(S) EN ";
		}
		
		function ocosto(costo,peso){
		//esta funcion obtiene el costo de un articulo
		//en base a su peso
		var costoc= costo*peso;
		return costoc;	
		}
		
		function regpeso(reng){
			//esta funcion registra en el html el costo de los articulos por peso
			var caspeso = document.getElementsByClassName("cajapeso");
			var nopesos = caspeso.length;
			var idart;
			var pesoact;
			var pesosum;
			var costoact;
			var costocalc;
			var costosum;
			var civa;
			var ivacalc;
			//inicializacion de suma costo y suma peso
			pesosum=0;
			costosum=0;
			for(var i = 0;i < nopesos; i++){
				idart= document.getElementById("id"+reng).innerHTML;
				pesoact = document.getElementById("peso"+i).value;
				costoact = document.getElementById("cost"+reng).innerHTML;
				civa = document.getElementById("civa"+reng).innerHTML;
				costocalc= ocosto(costoact,pesoact);
				costosum = costosum+costocalc;
				pesosum= pesosum+pesoact;
			}
			$("#pidepeso").popup("close");
			document.getElementById("costof"+reng).innerHTML= costosum.toFixed(2);
			document.getElementById("cpeso"+reng).innerHTML= pesosum;
			if(civa==1){
				ivacalc = calcivar(costosum,reng);
				document.getElementById("iva"+reng).innerHTML= ivacalc.toFixed(2);
			}
			calctot();
			quita();
		}
		
		function pidepeso(arts,reng){
			//esta funcion muestra el dialogo para registrar pesos
			//de arts recibidos.
			//agregar las unidades de peso
			var titpeso=document.getElementById("tpeso").innerHTML;
			var tud= document.getElementById("cud"+reng).innerHTML;
			document.getElementById("tpeso").innerHTML=titpeso+tud+"?";
			//agregar cajas para registrar peso
			var origen = document.getElementById("dialpeso");
			var boton = document.createElement("INPUT");
			boton.type ="BUTTON";
			boton.value = "LISTO";
			boton.id = "regpeso"
			for(var i=0;i<arts;i++){
				var divi = document.createElement("DIV");
				var caja = document.createElement("INPUT");
				caja.id = "peso"+i;
				caja.className= "cajapeso";
				caja.placeholder = "articulo "+(i+1);
				origen.appendChild(divi);
				divi.appendChild(caja);
			}
			origen.appendChild(boton);
			var cancela = document.getElementById("cancpeso");
			cancela.addEventListener('click',quita,false)
			var registra = document.getElementById("regpeso")
			registra.addEventListener('click',function(){
			regpeso(reng);
				},false)
			$("#pidepeso").popup("open");
			$("#peso0").focus();
		}
		
		function calcivar(costo,reng){
			//esta funcion calcula el iva de un art. y lo registra en HMTL
			var ivaact= costo *.16;
			document.getElementById("iva"+reng).innerHTML= ivaact.toFixed(2);
			return ivaact;	
		}
		
		function calcosto(reng){
		//esta funcion calcula el costo de un art. y su iva  y los registra en html
			var costoact;
			var cantact;
			var civa;
			costoact = document.getElementById("cost"+reng).innerHTML;
			cantact = document.getElementById("cant"+reng).value;
			civa = document.getElementById("civa"+reng).innerHTML;
			var costof= costoact*cantact;
			if (civa==1){
				calcivar(costof,reng);
			}
			document.getElementById("costof"+reng).innerHTML= costof.toFixed(2);
			calctot();
		}
		
		function sacareng(nombre){
			//esta funcion extrae el renglon de un nombre
			var longi = nombre.length;
			var remov = longi-3;
			var reng = nombre.slice(-remov);
			return reng;
		}
		
		function extraereng(renglon){
			//esta funcion extrae el renglon de un nombre
			//ver si se debe pesar el articulo
			var speso=document.getElementById("speso"+renglon).innerHTML;
			//si se pesa, presentar el cuadro de pesos
				if (speso==1){
					//el numero de articulos
					var narts = document.getElementById("cant"+renglon).value;
					//llama dialogo de peso
					pidepeso(narts,renglon);
				}else{
					calcosto(renglon);			
				}	
			}
			
		function calcreng(){
			//esta funcion calcula el no. de renglones
			var cantis;
			//calcula las cantidades totales
			cantis= document.getElementsByClassName("icant").length;
			return cantis;
		}

		function calctot(){
			//esta funcion calcula el contenido del renglon totales
			var cantot = [];
			var ivatot=[];
			var eleg;
			var cantact;
			var cantf ;
			var costot= [];
			var costact;
			var costf;
			var ivact;
			var ivaf;
			var arrno;
			var grantot;
			//cantidades
				for(var i = 0; i<cantreng;i++){
					cantact = parseInt(document.getElementById("cant"+i).value);
					costact = parseFloat(document.getElementById("costof"+i).innerHTML);
					ivact= parseFloat(document.getElementById("iva"+i).innerHTML);
					eleg = document.getElementById("chk"+i).checked;
					if(eleg==true){cantot.push(cantact);}
					costot.push(costact);
					ivatot.push(ivact)	;				
				}
				if(cantot.length>0){cantf= cantot.reduce(function(ant,act){return ant+=act;})}else{cantf= 0;}
				document.getElementById("ctot").innerHTML= cantf;
				costf= costot.reduce(function(cant,cact){return cant+=cact;});
				document.getElementById("mtot").innerHTML= costf.toFixed(2);
				ivaf=ivatot.reduce(function(iant,iact){return iant+=iact;});
				grantot=costf+ivaf;
				document.getElementById("itot").innerHTML= ivaf.toFixed(2);
				document.getElementById("gtot").innerHTML= grantot.toFixed(2);
		}
		
		function pesoa(){
			//esta funcion corre la rutina de dialogo para peso de articulos
			//si se oprime un check
			var valcheck= this.checked;
			var valnom = this.id;
			var reng = sacareng(valnom);
			if(valcheck==true){
				extraereng(reng);
			}else{//se corrige el costo
				document.getElementById("costof"+reng).innerHTML= "0.00";
				document.getElementById("iva"+reng).innerHTML= "0.00";
				// calcular totales
				calctot();
			}
		}
		
		function validacheck(){
			//esta funcion enciende una bandera si se eligio un check
				var checklist = document.getElementsByClassName("ichk")
				var resul = false;
				var node;
				var resp;
				 for (var i = 0; i < checklist.length; i++) {
   						 	node = checklist[i];
	        				resp = node.checked;
							if(resp==true){resul=true;}else{resp= false;}	
    					}
				return resul;	 
			}
			
			function recaba(reng){
				//esta funcion recaba los datos para su envio a las tablas
				var nart;
				var cant;
				var costo;
				var speso;
				var pesoact;
				var selec=[];
				nart = document.getElementById("id"+reng).innerHTML;
				selec.push(nart)
				cant = document.getElementById("cant"+reng).value;
				selec.push(cant);
				costo = document.getElementById("costof"+reng).innerHTML;
				selec.push(costo);
				speso = document.getElementById("speso"+reng).innerHTML;
				selec.push(speso);
				pesoact = document.getElementById("cpeso"+reng).innerHTML;
				selec.push(pesoact);
				
				return selec;
			}
			
			function revisacheck(){
				//esta funcion recorre los checks y toma los valores de los elegidos
				var node_list = document.getElementsByClassName("ichk");
				var dselec=[];
				var conta =0;
				for (var i = 0; i < node_list.length; i++) {
   						 var node = node_list[i];
	        			 var resp = node.checked;
							if(resp==true ){
									//si se eligio, obtener el reglon
									var rsel = sacareng(node.name);
									//añadir variables a arreglo
									dselec.push(recaba(rsel));
								}
								conta++;		
				}
				return dselec;
			}
			
			function tiporect(tiposurt){
				var texto;
				//esta funcion traduce el tipo de surtido a texto
				if(tiposurt == 10){texto="PARCIAL";}else if(tiposurt ==11){texto="TOTAL";}else{texto="ERROR";}
				return texto;
			}
			
		function evalua(resul,oc,tiporec){
			/** se evalua la respuesta del servidor**/
			switch (resul){
				case -90:
					var resp = "ERROR EN CONEXION A BD";
					break;
				case -99:
					var resp = "ERROR: LA ORDEN DE COMPRA YA ESTA EN INVENTARIO";
					break;
				case -1:
					resp = "ERROR EN REGISTRO DE ARTICULOS";
					break;
				case -2:
					resp = "ERROR EN REGISTRO CONTABLE";
					break;
				case -3:
				resp = "ERROR EN ACTUALIZACION DE OC";
				break;
				default:
					var textof = tiporect(tiporec);
					resp = "ORDEN DE COMPRA INGRESADA <br>Numero: "+ oc + "<br>"+"FORMA: "+textof;	
			}

			return resp;
		}
		 function registra(){
		 	var dseleco=[];
		 	//esta funcion manda los datos de recepcion a bd
		 		var resp =validacheck();
		 		    var fechaf=document.getElementById("fecha");
		 		    fechar=fechaf.value;
					if(resp==false){
					// se avisa que se debe oprimir un check
						aviso("NO SE RECIBIO NINGUN ARTICULO:<br>REVISE");	
					}else if(!isValidDate(fechar)){
						aviso("FALTA FECHA")
						fechaf.focus();
						}else{
						//recoge datos grales
						remi= document.getElementById("remi").value;
						fact = document.getElementById("fact").value;
						ivat= document.getElementById("itot").innerHTML;
						mtot= document.getElementById("mtot").innerHTML;
						//recorre los checks y anota los datos
		 				dseleco = revisacheck();
		 				//envia los datos
		 				$.post( "php/recibeoc.php",
						{	oc:para,
							remi:remi,
							fact:fact,
							monto:mtot,
							ivat:ivat,
							arts:dseleco,
							fechar:fechar,
						 }, null, "json" )
    						.done(function( data) {
    							var revst= data.resul;
    							var tiposur= data.tipos;
    							var noc = data.noc;
    							var textor = evalua(revst,noc,tiposur);
    							aviso(textor);
    							$( "#aviso" ).on( "popupafterclose", function( event, ui ) {
									window.location.href = "listoc.php";
								} );
    						})
    						.fail(function(xhr, textStatus, errorThrown) {
    							var err1 = data.success;
    							aviso("error alta de rec oc: "+xhr.responseText);
							});	
		 			}
		 }
		$( document ).on( "pageinit", "#pagrecoc", function( event ) {
			//obtener datos para la construccion
				para = getpar('oc');
			if(!para){
				aviso("NO HAY ORDEN DE COMPRA SELECCIONADA");
			//retraso para retirar la pantalla
				window.setTimeout(function(){window.location.href = "listoc.php";}, 2000);	
				
			}else{
				$.get('php/getlistprodoc.php',{oc:para},function(data){
					// extraer datos
						var liprodoc = JSON.parse(data);
						var noprods= liprodoc.length;
					//colocar titulo
							$("<H3>ORDEN DE COMPRA NO. "+para+"</H3>").insertAfter("H1");
					//fecha por defecto
	  				document.getElementById("fecha").valueAsDate = new Date();	
					//colocar renglones
						for (var i = 0; i < noprods; i++) {
							var nombre = liprodoc[i].nom;
							var cant = liprodoc[i].cant;
							var nart = liprodoc[i].idart;
							var speso= liprodoc[i].speso;
							var costo = liprodoc[i].costo;
							var civa = liprodoc[i].civa;
							var cud = liprodoc[i].ud;
							addreng(nombre,cant,nart,i,speso,costo,civa,cud);
							//adicion de escucha a check
							var estecheck = document.getElementById("chk"+i)
							estecheck.addEventListener('change',pesoa,false);
							var btnrecibe = document.getElementById("recibe")
							btnrecibe.addEventListener('click',registra,false);
						}
						//obtener cantidad de reglones con pag construida
						cantreng = calcreng();									
					});
				}
		});
					
	})();	
	</script>
	
</head>

<body>
	<div data-role="page" id="pagrecoc">
		<div data-role="header" id="cabezal">
			<a href="listoc.php" data-ajax="false" class="ui-btn-left ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-back">Regresar</a>
			<h1 id="titulo">RECEPCION DE ORDEN DE COMPRA</h1>
			<a href="logout.php" data-ajax="false" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
		</div>
		<div data-role = "ui-content" id="lista">
		<a href="#navpanel" class="ui-btn ui-shadow ui-corner-all ui-btn-inline ui-btn-icon-left ui-icon-bars">Navegaci&oacute;n</a>
			<form id="forma" method="post" enctype="application/x-www-form-urlencoded">
				<div class="cajacent">
				<h3 id="tpago"></h3>
					<label for ="fecha">Fecha:</label>
					 <input type="date" name="fecha" id="fecha">
				</div>
				<div class="ui-field-contain">
					<div class="cajamed">
						<label for="remi">Remisión:</label>
						<input type="text" id="remi" name="remi" >
						<label for="fact">Factura:</label>
						<input type="text" id="fact" name="fact">
					</div>
					
				</div>
				<fieldset class="ui-grid-d" id="tbrecoc">
					<div class="ocult">id</div>
				    <div class="ui-block-a"><div class="ui-bar ui-bar-b">Producto</div></div>
				    <div class="ui-block-b"><div class="ui-bar ui-bar-b">Cantidad</div></div>
				    <div class="ui-block-c"><div class="ui-bar ui-bar-b">OK</div></div>
				    <div class="ui-block-d"><div class="ui-bar ui-bar-b">Subtotal</div></div>
				    <div class="ui-block-e"><div class="ui-bar ui-bar-b">IVA</div></div>	    
				</fieldset>
				<fieldset class="ui-grid-d" id="tbtot">
					<div class="ui-block-a" id="rtot" name="rtot"><div class="ui-bar-a">TOTALES</div></div>
					<div class="ui-block-b" id="ctot" name="ctot"><div class="ui-bar-a">0</div></div>
					<div class="ui-block-c" id="tont" name="tont"><div class="ui-bar-a">  </div></div>
					<div class="ui-block-d" id="mtot" name="mtot"><div class="ui-bar-a">0.00</div></div>
					<div class="ui-block-e" id="itot" name="itot"><div class="ui-bar-a">0.00</div></div>
					<div class="ui-block-a"><div class="ui-bar-c"> </div></div>
					<div class="ui-block-b"><div class="ui-bar-c"> </div></div>
					<div class="ui-block-c"><div class="ui-bar-c"> </div></div>
					<div class="ui-block-d"><div class="ui-bar-a">GRAN TOTAL</div></div>
					<div class="ui-block-e" id="gtot" name="gtot"><div class="ui-bar-a">0.00</div></div>
				</fieldset>

			 <input data-theme="b" data-icon="check" data-iconshadow="true" value="Recibir" type="button" 
		    	name="recibe"id="recibe">	
			</form>
		</div>
		<div data-role="popup" id="aviso">
			<p>Sin texto, todavía.</p>
		</div>
		<div data-role ="popup" id="pidepeso" class="ui-content" data-dismissible="false" data-theme="b">
			<input type="button" data-theme="a" data-icon="delete" data-iconpos="notext" id="cancpeso"/>
			<label id="tpeso">PESO ARTICULO(S) EN </label>
			<div id="dialpeso">
				
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
