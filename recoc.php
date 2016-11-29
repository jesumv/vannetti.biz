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
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<link rel="shortcut icon" href="img/logomin.gif" />  
	<link rel="apple-touch-icon" href="img/logomin.gif">
	<link rel="stylesheet" href= "css/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href= "css/movil.css" />
	<script src="js/jquery.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	<script>
	'use strict';
	(function() {
		var arrcosto = [];
		function getpar(name){
		//esta funcion obtiene el numero de orden de compra del string GET
	   		if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
	      	return decodeURIComponent(name[1]);
		}
		
		function aviso(texto){
			//esta funcion enciende el aviso de la pagina con el aviso
			//pasado como parametro.
			$("#aviso").html(texto);
			$("#aviso").popup("open");
		}
		
		function addreng(nombre,cant,idprod,reng,speso,costo){
			var origen = document.getElementById("tbrecoc");
			var nombre1 = document.createElement("DIV");
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
				
			for(var z=0;z<7;z++){
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
				    	clase2 = "";
				    	texto = speso;
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
						$("#cant0").focus();	
			}
		}
		
		
		function quita(){
	  		//quitar cajas de peso
			 var pesos = document.getElementById('dialpeso').getElementsByTagName("input");
			 var x = pesos.length;
			 $("#pidepeso").popup("close");
			 for(var i=0; i<x; i++) {
			 	pesos[0].parentNode.removeChild(pesos[0]);
			 }
		}
		
		function ocosto(costo,peso){
		//esta funcion obtiene el costo de un articulo
		//en base a su peso
		var costoc= costo*peso;
		return costoc;	
		}
		
		function regpeso(reng){
			//esta funcion anade al arreglo los arts correspondientes
			var caspeso = document.getElementsByClassName("cajapeso");
			var nopesos = caspeso.length;
			var idart;
			var pesoact;
			var costoact;
			var costocalc;
			var costosum;
			for(var i = 0;i < nopesos; i++){
				idart= document.getElementById("id"+reng).innerHTML;
				pesoact = document.getElementById("peso"+i).value;
				costoact = document.getElementById("cost"+reng).innerHTML;
				costocalc= pesoact*costoact;
				arrcosto.push([idart,pesoact,costocalc.toFixed(3)]);
			}
			console.log(arrcosto);
			console.log(arrcosto[0]);
			$("#pidepeso").popup("close");
			quita();
			
		}
		
		function pidepeso(arts,reng){
			//esta funcion muestra el dialogo para registrar pesos
			//de arts recibidos.
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
			registra.addEventListener('click',function(){regpeso(reng);},false)
			$("#pidepeso").popup("open");
			$("#peso0").focus();
		}
		
		function extraereng(nombre){
			//esta funcion extrae el renglon de un nombre
			var reng = nombre.slice(-1);
			//ver si se debe pesar el articulo
			var speso=document.getElementById("speso"+reng).innerHTML;
			//si se pesa, presentar el cuadro de pesos
				if (speso==1){
					//el numero de articulos
					var narts = document.getElementById("cant"+reng).value;
					pidepeso(narts,reng)
				}	
			}
		
		function pesoa(){
			//esta funcion corre la rutina de dialogo para peso de articulos
			//si se oprime un check
			var valcheck= this.checked;
			var valnom = this.id;
			if(valcheck==true){
				extraereng(valnom);
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
			
		 function registra(){
		 	//esta funcion manda los datos de recepcion a bd
		 		var resp =validacheck();
					if(resp==false){
					// se avisa que se debe oprimir un check
						aviso("NO SE RECIBIO NINGUN ARTICULO:<br>REVISE");	
					}else{
		 				alert("registrando");
		 			}
		 }
		$( document ).on( "pageinit", "#pagrecoc", function( event ) {
			//obtener datos para la construccion
				var para = getpar('oc');
				if(!para){
				aviso("NO HAY ORDEN DE COMPRA SELECCIONADA");
				window.setTimeout(function(){window.location.href = "listoc.php";}, 2000);	
				
			}else{
				$.get('php/getlistprodoc.php',{oc:para},function(data){
					// extraer datos
						var liprodoc = JSON.parse(data);
						var noprods= liprodoc.length;
					//colocar titulo
							$("<H3>ORDEN DE COMPRA NO. "+para+"</H3>").insertAfter("H1");
					//colocar renglones
						for (var i = 0; i < noprods; i++) {
							var nombre = liprodoc[i].nom;
							var cant = liprodoc[i].cant;
							var nart = liprodoc[i].idart;
							var speso= liprodoc[i].speso;
							var costo = liprodoc[i].costo;
							addreng(nombre,cant,nart,i,speso,costo);
							//adicion de escucha a check
							var estecheck = document.getElementById("chk"+i)
							estecheck.addEventListener('change',pesoa,false);
							var btnrecibe = document.getElementById("recibe")
							recibe.addEventListener('click',registra,false);
						}
															
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
				<fieldset class="ui-grid-c" id="tbrecoc">
					<div class="ocult">id</div>
				    <div class="ui-block-a"><div class="ui-bar ui-bar-b">Producto</div></div>
				    <div class="ui-block-b"><div class="ui-bar ui-bar-b">Cantidad</div></div>
				    <div class="ui-block-c"><div class="ui-bar ui-bar-b">OK</div></div>
				    <div class="ui-block-d"><div class="ui-bar ui-bar-b">Costo</div></div>
				    
				</fieldset>
				<fieldset class="ui-grid-c" id="tbtot">
					<div class="ui-block-a" id="rtot" name="rtot"><div class="ui-bar-a">TOTALES</div></div>
					<div class="ui-block-b" id="ctot" name="ctot"><div class="ui-bar-a">0</div></div>
					<div class="ui-block-c" id="tont" name="tont"><div class="ui-bar-a">  </div></div>
					<div class="ui-block-d" id="mtot" name="mtot"><div class="ui-bar-a">0.00</div></div>
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
			<label >PESO ARTICULO(S)?</label>
			<div id="dialpeso">
				
			</div>
			 
		</div>
		
		<div data-role="panel" id="navpanel" data-display="overlay">
	 		<ul data-role ="listview">
	 			<li><a href="oc.php" data-ajax="false">Ordenes de Compra</a></li>
	 			<li><a href="listoc.php" data-ajax="false">Rec. de OC</a></li>
		    	<li><a href="pedido.php" data-ajax="false">Pedidos</a></li>
		    	<li><a href="listasp.php" data-ajax="false">Listas de Productos</a></li>
	 		</ul>	    	
 		</div>
	</div>
	
</body>
</html>
