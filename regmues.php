<?php
  function __autoload($class){
	  require('include/' . strtolower($class) . '.class.php');
    }
    
//funciones auxiliares
require 'include/funciones.php';
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
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<link rel="shortcut icon" href="img/logomin.gif" />  
	<link rel="apple-touch-icon" href="img/logomin.gif">
  <title>Vanneti Cucina</title>
   <link rel="stylesheet" href= "css/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href= "css/movil.css" />
	<script src="js/jquery.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
   <script>
   	'use strict';
   	(function() {

   	//preparacion de fecha = hoy por defecto
		   Date.prototype.toDateInputValue = (function() {
			    var local = new Date(this);
			    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
			    return local.toJSON().slice(0,10);
			});
   		
   		   	function acant(){
   		   		//recoge datos y calcula costo
   		   		var noprod=document.getElementById("prod").innerHTML;
   				if(noprod!=""){
	   				var cant = document.getElementById("cantm").value;
	   		   		if (cant!=""){
	   		   			var costo= document.getElementById("costo"+noprod).innerHTML;
	   					var costot= cant*costo;
	   					document.getElementById("costom").value=costot;
	   					document.getElementById("recepm").focus();
	   		   		}else{
	   		   			document.getElementById("costom").value="";
	   		   		}
   					
   				}
 		
   			}
   			
   		$(document).on("pagecreate","#pagmues",function(event){
   			$("#listap").on("filterablebeforefilter",function(e,data){
   				var $ul=$(this),
   				$input=$(data.input),
   				value =$input.val(),
   				html="";
   				$ul.html( "" );
   				if ( value && value.length > 2 ) {
	   				$ul.html("<li><div class='ui-loader'><span class='ui-icon ui-icon-loading'></span></div></li>");
	   				$ul.listview( "refresh" );
	            	$.ajax({
	                url: "php/getlistprodt.php",
	                dataType: "json",
	                data: {
	                    	q: $input.val()
	                	}
	            	})
	            	.then( function (response) {
	            		var nombrc;
	            		var idprod;
	            		var costo;
		                $.each( response, function ( i,datos) {
			                	nombrc=datos['nombrc'];
			                	idprod=datos['idprod'];
			                	costo=datos['costo'];
		                    html += "<li><a class='bmues' id='"+idprod+"' href='#'>" +nombrc+"</a><span class='ocult' id='costo"+idprod+"'>"+costo+"</span></li>";
		                });
		                $ul.html( html );
		                var bmues= document.getElementsByClassName("bmues");
		                
		                function dmuestra(prod){
		                	//agrega el no producto al html
		                	var este = document.getElementById("prod");
		                	este.innerHTML=prod;
		                	var selectedItem = $(this).html();
      						$(this).parent().parent().find('input').val(selectedItem);
      						document.getElementById("fmues").focus();
      						$('#listap').hide();     
		                }
		                for (var i = 0; i < bmues.length; i++) {
			  					bmues[i].addEventListener('click', function(){
			  						dmuestra(this.id);
			  						}, false)
			  				}
		                $ul.listview( "refresh" );
		                $ul.trigger( "updatelayout");
               		}).fail(function(xhr, status, error) {
        					console.log(error);
    					});
            	};			
   			});
   			
		   /*****************************************************************************
		   *
		   * Metodos para actualizar/refrescar la IU
		   *
		   ****************************************************************************/  
		  	//fecha por defecto
		  	document.getElementById("fmues").value = new Date().toDateInputValue();
			//enfoque inicial
				document.getElementById("prodm").focus();  
			//metodos de los elementos de la pagina
			function aviso(texto){
				//esta funcion enciende el aviso de la pagina con el aviso
				//pasado como parametro.
				$("#aviso").html(texto);
				$("#aviso").popup("open");
			}
			function valida(fecha){
				//valida fecha
		   	    var fechac=isValidDate(fecha)
		   		if(!fechac){return -1;}else{return 0;}
		   	}
		   	
		   	function vfecha (){
		   		var fechaac=document.getElementById("fmues").value;
   		   		var resulfecha= valida(fechaac);
   		   		if(resulfecha==0){
   		   			document.getElementById("cantm").focus();
   		   		}else{
   		   			aviso("INTRODUZCA FECHA VALIDA (YYY-mm-dd)")
   		   			document.getElementById("fmues").focus();
   		   		}
   		   	}

			function evaluar(respuesta){
				//evalua la respuesta del servidor PHP
				var resp;
				switch(respuesta){
					case -1:
						resp = "ERROR EN REGISTRO GENERICO";
						aviso(resp);
						break;
					case -2:
						resp = "ERROR EN REGISTRO CONTABLE";
						aviso(resp);
						break;
					break;
					default:
						resp="REGISTRO DE MUESTRA OK";
						aviso(resp);
						$( "#aviso" ).on( "popupafterclose", function( event, ui ) {
									location.reload();
						} );

				}
					
			}

   			function enviamues(){
   				//envio de gasto a la base de datos
   				//recoleccion de variables
   				var prod= document.getElementById('prod').innerHTML;
   				var fecha = document.getElementById('fmues').value;
   				var cant = document.getElementById('cantm').value;
   				var costo = document.getElementById('costom').value;
   				var recep = document.getElementById('recepm').value;
   				var recepm=recep.toUpperCase();
   				//envio a bd
   					$.post( "php/enviamues.php",
							{	prod:prod,
								fecha:fecha,
								cant:cant,
								costo:costo,
								recep:recepm						
							 }, null, "json" )
							  .done(function(data) {
	    							var resul= data.resul;
	    							evaluar(resul);
	    						})
	    						.fail(function(xhr, textStatus, errorThrown ) {		
	    							document.write("ERROR EN REGISTRO MUESTRA: "+ errorThrown);
								}); 				
   			}
   			
			function validat(){
				var resul = 0;
				//validaciones
				var prod1= document.getElementById("prodm").value;
				if(prod1==""){resul = -1
								return resul;};
				var fech1=document.getElementById("fmues").value;
   				var valm=valida(fech1);
   				if(valm==-1){resul =-2
   								return resul;};
   				var cant1=document.getElementById("cantm").value;
   				if(cant1==""){resul = -3
   								return resul;};
				var recep1=document.getElementById("recepm").value;
   				if(recep1==""){resul = -4
   								return resul;};
				return resul;
			}
			
			
   			function regm(){
   				//funcion al apretar el boton de registrar muestra
				var resul1= validat();
   				
   				switch(resul1){
   					case -1:
   					aviso("ELIJA UN PRODUCTO");
   					document.getElementById("prodm").focus();
   					break;
   					case -2:
   					aviso("POR FAVOR INTRODUZCA FECHA VALIDA (AAAA/MM/DD)");
   					document.getElementById("fmues").focus();
   					break;
   					case -3:
   					document.getElementById("cantm").focus();
   					aviso("POR FAVOR INTRODUZCA UNA CANTIDAD");
   					break;
   					case -4:
   					aviso("POR FAVOR INTRODUZCA UN RECEPTOR");
   					document.getElementById("recepm").focus();
   					break;
   					default:
   					enviamues();
   					
   					
   				}
   			}
   			
   			function cancelam(){
   				document.getElementById('rmues').reset();
   			}
   			
   			function textoinput(){
   				 var text = $(this).text();
    			$(this).closest("ul").prev("form").find("input").val(text);
   			}
   			
   			//escuchas
   			//cajon de cantidad
   			document.getElementById("cantm").addEventListener('change',acant,false)
   			//cajon de fecha
   			document.getElementById("fmues").addEventListener('change',vfecha,false)
   			//registrar muestra
			document.getElementById("regm").addEventListener('click',regm,false)
			//boton cancela muestra
			document.getElementById("cancelm").addEventListener('click',cancelam,false)

   		 });

   	})();
   </script>
   <script src="js/fauxcx.js"></script>
</head>
	<body>
		<div data-role="page" id="pagmues"> 
			<div data-role="header">
				<a href="portalmov.php" data-ajax="false" class="ui-btn-left ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-home">Inicio</a>
					<h1>Salida de Muestras</h1>
    			<a href="logout.php" data-ajax="false" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
			</div>
			
			<div  role="main " class="ui-content">
				<a href="#navpanel" class="ui-btn ui-shadow ui-corner-all ui-btn-inline ui-btn-icon-left ui-icon-bars">Navegaci&oacute;n</a>
				<form method="post" enctype="application/x-www-form-urlencoded" name="rmues" id="rmues">
					<div class="cajacent">
						<div id="prod" class="ocult"></div>
						<label>Producto:</label>
						<form class="ui-filterable">
							 <input id="prodm" data-type="search" placeholder="Nombre del Producto...">
						</form>	
						<ul id="listap" name="listap" data-role="listview" data-inset="true" data-filter="true" data-input="#prodm"></ul>
					</div>
					<div class="cajacent">
						<label>Fecha: </label><input type="date" name="fmues"  id="fmues" class="cajam"/>
					</div>
					<div class="cajacent">
						<label>Cantidad:</label><input type="number" name="cantm" id="cantm" min="1"/>
					</div>
					<div class="cajacent">
						<label>Costo:</label><input type="text" name="costom" id="costom" disabled="true"/>
					</div>
					<div class="cajacent">
						<label>Receptor: </label><input type="text" name="recepm"  id="recepm" class="cajam" maxlength="20"/>	
					</div>
			
					<div >
	    				<input data-theme="b" data-icon="check" data-iconshadow="true" value="Enviar" type="button" 
	    				name="regm"id="regm">
	    				<input data-theme="b" data-icon="delete" data-iconshadow="true" value="Cancelar" type="button" 
	    				name="cancelm"id="cancelm">
				    </div>
				</form>
				<div data-role="popup" id="aviso">
					<p>Sin texto, todavía.</p>
				</div>
<!--main-->
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
<!--page-->
		</div>	   	 
	</body>
</html>
