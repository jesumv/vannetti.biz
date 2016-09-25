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
<html lang="en">
<head>
  <meta charset="utf-8">

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
	<script src="js/jquery.number.js"></script>
	<script>
		function getpar(name){
	   		if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
	      	return decodeURIComponent(name[1]);
		}
		
		function aviso(texto){
			//esta funcion enciende el aviso de la pagina con el aviso
			//pasado como parametro.
			$("#aviso").html(texto);
			$("#aviso").popup("open");
		}
		
			$(document).ready(function() {
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
							var texto = cant+" "+ nombre;
							var conten = "<label><input type='checkbox' id='in"+i+"'>"+texto+"</label><input type='hidden' name='pr"+i+
					"' id='pr"+i+"' value="+nart+">";
						$("#campos").append(conten);
						$("#campos").enhanceWithin();	  						}					
					});
			function validacheck(etiq){
			//esta funcion enciende una bandera si se eligio un check
				var node_list = document.getElementsByTagName(etiq);
				var resul = false;
				 for (var i = 0; i < node_list.length; i++) {
   						 var node = node_list[i];
    					if (node.getAttribute('type') == 'checkbox') {
	        					var resp = node.checked;
							if(resp==true && resul==false){resul=true;}	
    					}
				}
				return resul;	 
			}
			
			function revisacheck(){
				//esta funcion recorre los checks y toma los valores de los elegidos
				var node_list = document.getElementsByTagName("input");
				var selec=[];
				var conta =0;
				for (var i = 0; i < node_list.length; i++) {
   						 var node = node_list[i];
    					if (node.getAttribute('type') == 'checkbox') {
	        					var resp = node.checked;
							if(resp==true ){
								var rsel = document.getElementById('pr'+conta);
								selec.push(rsel.value);
								}
								conta++;		
    					}
				}
				return selec;
			}
			
			function tiporect(tiposurt){
				var texto;
				//esta funcion traduce el tipo de surtido a texto
				if(tiposurt == 2){texto="PARCIAL";}else if(tiposurt == 3){texto="TOTAL";}else{texto="ERROR";}
				return texto;
			}
			
			function rresprrecoc(respuesta,oc,tiporec){
			//esta funcion revisa el mensaje de respuesta y lo traduce
			switch (respuesta){
				case -99:
					var resp = "LA ORDEN DE COMPRA YA INGRESO A INVENTARIO";
					break;
				case -1:
					resp = "ERROR EN REGISTRO DE ARTICULOS";
					break;
				case -2:
					resp = "ERROR EN REGISTRO CONTABLES";
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
		
		
			$("#recibe").click(function(){
				// validar que esté oprimido al menos 1check
				var resp =validacheck('input');
					if(resp==false){
					// se avisa que se debe oprimir un check
						aviso("NO SE RECIBIO NINGUN ARTICULO");	
					}else{
					//recoleccion de datos
						var prs = revisacheck();
					// se envian los datos a registro
						$.post( "php/recibeoc.php",
						{	oc:para,
							arts:prs,
						 }, null, "json" )
    						.done(function( data) {
    							var noc= data.noc;
    							var tiporec = data.tipos;
    							var resp2 = data.resp;
    							var cad = rresprrecoc(resp2,noc,tiporec);
								aviso(cad);
								$( "#aviso" ).on( "popupafterclose", function( event, ui ) {
									window.location.href = "listoc.php";
								} );
    						})
    						.fail(function( data ) {
    							var err1 = data.success;
    							aviso("error alta de rec oc: "+err1);
							});	
					}	
					});		
					}
					});
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
			<form id="forma" method="post"
			enctype="application/x-www-form-urlencoded">
			<fieldset data-role="controlgroup" id="campos">
				<legend>PRODUCTOS:</legend>
			</fieldset>
			 <input data-theme="b" data-icon="check" data-iconshadow="true" value="Recibir" type="button" 
		    name="recibe"id="recibe">	
			</form>
		</div>
		<div data-role="popup" id="aviso">
		<p>Sin texto, todavía.</p>
		<div data-role="panel" id="navpanel" data-display="overlay">
	 		<ul data-role ="listview">
	 			<li><a href="oc.php" data-ajax="false">Ordenes de Compra</a></li>
	 			<li><a href="listoc.php" data-ajax="false">Rec. de OC</a></li>
		    	<li><a href="pedido.php" data-ajax="false">Pedidos</a></li>
		    	<li><a href="listasp.php" data-ajax="false">Listas de Productos</a></li>
	 		</ul>	    	
 		</div>
	</div>
	</div>
</body>
</html>
