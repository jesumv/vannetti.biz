<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	
	  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
	       Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Vannetti Cucina</title>
	<meta name="author" content="jmv">
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
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
	var arre = document.getElementsByTagName("INPUT");
	var longit = (arre.length)-2;
	for(var z=3; z<longit; z++){
		var cantact = arre[z].value;
		var cantf = validacant(cantact);
		cantt = cantt + cantf;
	}
	return cantt;
}

function sumaprecio(){
	//esta funcion suma los precios
	var preciot =0;
	var arre = document.getElementsByClassName("subt") ;
	var longit = arre.length;
	for(var z=0; z<longit; z++){
		var preact = Number(arre[z].innerText);
		var preciot = preciot + preact;
	}
	return preciot;
}
	
	function multiplica(){
		//esta funcion obtiene el precio final en base a los datos de cantidad
		//se valida si la entrada es numerica
	 	var checa = checaval(this.value);
	 	var cad = this.name;
	 	if (checa==true) {
	 		//se toma el precio oculto, se multiplica x cantidad y se 
			//agrega el resultado a la tabla
			var longi = this.name.length;
			var pos = cad.indexOf("k");
			var rengl = cad.slice(pos+1);
			var precio = document.getElementById("precio"+ rengl).innerHTML;
			var valor = document.getElementById("chk"+ rengl).value;
			var preciot = precio*valor;
			document.getElementById("psubt"+ rengl).innerHTML = $.number(preciot,2);
			// se modifican los totales
			var sumacants = sumacant();
			document.getElementById("sumcantp").innerHTML = sumacants;
			var sumaprecs = sumaprecio();
			var sumapreciost= $.number(sumaprecs,2);
			document.getElementById("sumtotp").innerHTML = sumapreciost;
	 	}else{;
		 aviso("debe introducir una cantidad positiva");
		$( "#aviso" ).on( "popupafterclose", function( event, ui ) {
				var enfoc = document.getElementById(cad);
				enfoc.value = "";
				enfoc.focus();
			} );
	 };
	};
		function addrengart(id,categ,nombre,precio,linea){
			//esta funcion agrega los renglones de articulos para una categoría.
			var ancla = document.getElementById("cat"+categ);
		//celdas ocultas
		//id
		var idr = document.createElement("DIV");
			idr.className = "ocult";
			idr.id = "id"+linea;
			idr.name = "id"+linea;
			var noder = document.createTextNode(id);
			idr.appendChild(noder);
			ancla.appendChild(idr);
		//precio
			var precior = document.createElement("DIV");
			precior.className = "ocult";
			precior.id = "precio"+linea;
			precior.name = "precio"+linea;
			var node = document.createTextNode(precio);
			precior.appendChild(node);
			ancla.appendChild(precior);
			
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
						addrengart(idprod,grupo,nombre,precio,i);
					}			
				});
		}
		function agregacab(linea){
	//esta funcion agrega los titulos del grid
			var envolt = document.createElement("DIV");
			envolt.className = "ui-grid-b";
			envolt.id = "cat"+linea;
			for(var i=0;i<3; i++){
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
			envolt.className = "ui-grid-b";
			for(var i=0;i<3; i++){
					var ancla = document.getElementById("totales");
					var atr;
					var texto;
					var rid;
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
							atr="ui-block-c";
							texto="0.00";
							rid="sumtotp";
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
					var atrib= document.createAttribute("data-role");
					atrib.value = "collapsible";
					barra.setAttributeNode(atrib);
					var tema = document.createAttribute("data-theme");
					tema.value="a";
					var temaint	= document.createAttribute("data-content-theme");
					temaint.value ="a";
					barra.setAttributeNode(tema);
					barra.setAttributeNode(temaint);
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
		//esta funcion valida el elemento que se pasa como argumento regresando 0 si el elemento
		//coincide o es nulo
		if(document.getElementById(elem)=== null){resul = 0;}else{
			var texto = document.getElementById(elem).innerHTML;
			if(texto==valor){var resul = 0;}else{resul = -1;}
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
			   
			   $("#pcancela").click(function(){
			   		location.reload();
			   		$('#pcte').selectmenu( "enable" );
			   });
			
		$( document ).on( "pageinit", "#pedpag", function( event ) {
			//modificacion de las etiquetas del flipswitch
			$( "#fact" ).flipswitch({
  				onText: "Si",
  				offText:"No"
			});
			//lenar el combo de clientes
	  			llenactes();
	  		//checar si se eligió cliente	
	    	//agregar las categorías de productos
	    		document.getElementById('pcte').addEventListener('change', function() {	
					//se muestra tabla
					llenacats();	
					hazvisib(true);
					//agregar estilo mobile
					$("#pedpag").enhanceWithin();
					//desenchufar select
					$('#pcte').selectmenu( "disable" );
		  	});	
	
	  		//escucha del boton enviar
	  		document.getElementById('penvia').addEventListener('click',function(){
	  			//revisar que se hayan introducido datos
	  			var resul1 = validaelem("sumtotp","0.00");
	  			if(resul1==0){
	  				aviso("No ha solicitado ningun articulo");
					$("#aviso").on( "popupafterclose", function( event, ui ) {
							if($("#chk0")){$("#chk0").focus();}
						} );
	  			}else{
	  				//recolección de variables
	  					var cte = $("#pcte").val();
	  					var longi = $(".cantp").length;
	  					var total =$("#sumtotp").text();
	  					var totarts =$("#sumcantp").text();
	  					var prods=[];
						var cant=[];
						var preciou=[];
						var preciot=[];
						var facturarp = $("#fact").prop("checked");
				//variable para controlar el no. de renglon.
						var longcants = 0;
						for(var z=0; z <longi; z++){
							if($("#chk".concat(z)).val()!==""){
								prods[longcants] = $("#id".concat(z)).text();
								cant[longcants] = $("#chk".concat(z)).val();
								preciou[longcants]= $("#precio".concat(z)).text();
								preciot[longcants]= $("#psubt".concat(z)).text();
								longcants++;
							}
						}
						longcants--;
	  				//revisar que tipo de venta es
		  				var tipoventa = tipovta();
		  			//si todo ok, se envian los datos
						$.post( "php/enviaped.php",
						{	cte:cte,
							longi:longcants,
							prods:prods,
							cants:cant,
							preciou:preciou,
							preciot:preciot,
							totarts:totarts,
							total:total,
							tipoventa:tipoventa,
							facturarp:facturarp
							
						 }, null, "json" )
    						.done(function( data) {
    							var nped= data.nped;
    							var arts =data.arts;
    							var total = data.total;
    							var cad = "PEDIDO OK <br>Numero: "+ nped + "<br>"+"Arts: "+arts+"<br>"+"total: "+total;
								aviso(cad);
								$( "#aviso" ).on( "popupafterclose", function( event, ui ) {
									location.reload();
									$('#pcte').selectmenu( "enable" );
								} );
    						})
    						.fail(function( data ) {
    							var err1 = data.success;
    							aviso("error alta de pedido: "+err1);
							});	
		  			
	  			}
	  			
	  		});	
		});
	})();	
	</script>
</head>

<body>
  <div data-role="page" id="pedpag"> 
	<div data-role="header">
    	<a href="portalmov.php" data-ajax="false" class="ui-btn-left ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-home">Inicio</a>
		<h1>Toma de Pedido</h1>
    	<a href="logout.php" data-ajax="false" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
	</div>

    <div role="main " class="ui-content">
			<a href="#navpanel" class="ui-btn ui-shadow ui-corner-all ui-btn-inline ui-btn-icon-left ui-icon-bars">Navegaci&oacute;n</a>
		<form  method="post"
		enctype="application/x-www-form-urlencoded" name="pforma" id="pforma">
			<div>
				<label for="pcte" class="select">Cliente</label>
				<select name="pcte" id="pcte">
			    	<option value="0">Seleccione al Cliente</option>
				</select>
			</div>
			<div id="ocultable" class="tablaoculta">
				<fieldset id="tventa" name="tventa" data-role="controlgroup" data-type="horizontal">
		    		<legend>Tipo de Venta</legend>
		    		<label>
		    			<input type="radio" id="rcredcon-a" name="rcredcon" value= "0" checked="checked"/>Contado
		    		</label>
		    		<label>
		    			<input type="radio" id="rcredcon-b" name="rcredcon" value= "1" />Crédito
		    		</label>
    			</fieldset>	
	    		<label for"fact">Facturar?</label>
	    			<input type="checkbox" data-role="flipswitch" id="fact" name="fact"/>
		
				<div id="ptabla">
				</div>	
			     	<input data-theme="b" data-icon="check" data-iconshadow="true" value="Enviar" type="button" 
				    name="penvia"id="penvia">
				    <div class="ui-input-btn ui-btn ui-btn-b ui-icon-delete ui-btn-icon-left ui-shadow-icon" 
				    name="pcancela"id="pcancela">
				        Cancelar
				    	<input data-enhanced="true" value="Enhanced" type="button">
			    	</div>
			</div>
		</form>

    
		<div data-role="popup" id="aviso">
		<p>Sin texto, todavía.</p>
		</div>
  	</div>
  		<div data-role="panel" id="navpanel" data-display="overlay">
 		<ul data-role ="listview">
 			<li><a href="oc.php" data-ajax="false">Ordenes de Compra</a></li>
 			<li><a href="listoc.php" data-ajax="false">Rec. de OC</a></li>
	    	<li><a href="pedido.php" data-ajax="false">Pedidos</a></li>
	    	<li><a href="listasp.php" data-ajax="false">Listas de Productos</a></li>
	    	<li><a href="portal.php" data-ajax="false">Portal</a></li>
 		</ul>	    	
 		</div>
  </div>
</body>
</html>