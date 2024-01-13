
<?php
function myAutoload($ClassName)
{
    require('include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
/*** checa login***/
       $funcbase->checalogin($mysqli);
    } else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
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
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link rel="shortcut icon" href="img/logomin.gif" />  
	<link rel="apple-touch-icon" href="img/logomin.gif">
	<link rel="stylesheet" href= "css/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href= "css/movil.css" />
	<script src="js/jquery.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	<script src="js/jquery.number.js"></script>
	<script src="js/fauxnum.js"></script>
	
	<script>

	'use strict';
	(function() {
		//variables globales
		var actprecio=0;
		//tipo para fecha default hoy
		Date.prototype.toDateInputValue = (function() {
    			var local = new Date(this);
    			local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
    			return local.toJSON().slice(0,10);
		});
		
		$(document).on( "pageinit", "#ocpag", function( event ) {
			//fecha por default
			$('#fecha').val(new Date().toDateInputValue());
			//llena la lista de proveedores
			llenaop();	
			//traer funciones auxiliares
          function responde(){
                	//mensajes de texto en respuesta a validacion
                	aviso("No ha solicitado ningun articulo");
                	$( "#aviso" ).on( "popupafterclose", function( event, ui ) {
                			if($("#cant0")){$("#cant0").focus();}
                		} );
                }

            function valorradio(elem){
                //obtiene el valor de un boton radio
                //recibe el nombre del elemento como string
                var radios = document.getElementsByName(elem);

                        for (let i = 0, length = radios.length; i < length; i++) {
                          if (radios[i].checked) {
                            // do whatever you want with the checked radio
                            return radios[i].value;      
                            // only one radio can be logically checked, don't check the rest
                            break;
                          }
                        }
                }

            
			function regoc(){
				//funcion para registrar una orden de compra al recibirla
				// y afectar inventarios y cuenta de abono diario
				//evaluacion de tipo de venta
					let cred= valorradio("rpcredcon");
					//recoleccion de variables
					let fecha = document.getElementById("fecha").value;
					let prov = document.getElementById("ocprov").value;
					let stotal = document.getElementById("subtotalo").innerHTML;
					let imps= document.getElementById("timp").innerHTML;
					let total = document.getElementById("ttotal").innerHTML;
					//definicion de tipo de pago dependiendo si es credito o no-credito no se sabe forma
					let tpago2=valorradio("rtpago");
					let tpago;
					if(cred==1){tpago = 99}else{tpago= tpago2} ;				
					let fact = document.getElementById("factp").checked?1:0;
					let cambiatp=document.getElementById("cambiap").checked?1:0;
					let factura = document.getElementById("factura").value;
					let ctapago = document.getElementById("ctapago").value;
					let longi = document.getElementsByClassName("cost").length;
					let prods=[];
					let cant=[];
					let preciou=[];
					let preciot=[];
					let civai=[];
					let ciepsi=[];
					let cambiapi=[];
					let longcants = 0;
					let canta;
					for(let z=0; z <longi; z++){
						canta=document.getElementById('cant'+z).value;
						if( canta!==""){
							prods[longcants] = document.getElementById('id'+z).innerHTML;
							cant[longcants] = canta;
							preciou[longcants]= document.getElementById('cost'+z).value;
							preciot[longcants]= document.getElementById('subtoc'+z).innerHTML;
							civai[longcants]= document.getElementById('civa'+z).innerHTML;
							ciepsi[longcants]= document.getElementById('cieps'+z).innerHTML;
							cambiapi[longcants]= document.getElementById('cambio'+z).innerHTML;
							longcants++;
						}
					}
					longcants--;
					//suma de impuestos
					let totiva= civai.reduce(sumaarr, 0);
					let totieps= ciepsi.reduce(sumaarr, 0);
					//si todo ok, se envian los datos de recepcion de la oc, y pago en su caso
					$.post( "php/enviarecoc.php",
					{	fecha:fecha,
						prov:prov,
						cred:cred,
						tpago:tpago,
						fact:fact,
						cambiatp:cambiatp,
						factura:factura,
						ctapago:ctapago,
						imps:imps,
						stotal:stotal,
						total:total,
						prods:prods,
						cants:cant,
						preciou:preciou,
						preciot:preciot,
						totiva:totiva,
						totieps:totieps,
						cambiapi:cambiapi
					 }, null, "json" )
						.done(function(data) {
							//se revisa si el codigo de regreso es 0
							if(data.resultado==0){
								let noc= data.noc;
								let arts =data.arts;
								let total = $.number(data.total,2);
								let cad = "Numero: "+ noc + "<br>"+"Arts: "+arts+"<br>"+"total: "+total
								+"<br>";
								let cambiop=data.cambiop;
								cambiop==0?cad=cad+"COSTOS ACTUALIZADOS":"";
								// si se cambiaron precios, se envia mensaje
								confirma(cad,noc);
								$( "#confirma" ).on( "popupafterclose", function( event, ui ) {
									location.reload();
									$('#ocprov').selectmenu( "enable" );
								} );
								
								}else{
									//proceso no ok
            						let guion="Error:"+data.mensaje+" codigo:"+data.resultado;
                    				aviso(guion);

									}
							
							

						})
						.fail( function(xhr, textStatus, errorThrown) {
							aviso(xhr.responseText);
						});
				}


//eventos para los botones de accion
			function enviaoc(){
				//alta de oc sin afectacion a inventarios ni diario boton enviar
				//validaciones previas al registro
				let resul1 = validaelem("tcant","0");
				//si no hay datos que enviar
				if(resul1== 0){
						responde();
					}else{
						//recoleccion de variables
						let prov = document.getElementById("ocprov").value;
						let stotal = document.getElementById("subtotalo").innerHTML;
						let total = document.getElementById("ttotal").innerHTML;
						let imps = document.getElementById("timp").innerHTML;
						let cred = valorradio("rpcredcon");
						let tpago2=valorradio("rtpago");
						let tpago;
						if(cred==1){tpago = 99}else{tpago= tpago2};			
						let fact = document.getElementById("factp").checked?1:0;;
						let cambiatp=document.getElementById("cambiap").checked?1:0;
						let longi=document.getElementsByClassName("cost").length;
						let prods=[];
						let cant=[];
						let preciou=[];
						let preciot=[];
						let cambiapi = [];
						let longcants = 0;
						let canta;
						for(let z=0; z <longi; z++){
							canta= document.getElementById('cant'+z).value;
							if(canta!==""){
								prods[longcants] = document.getElementById('id'+z).innerHTML;
								cant[longcants] = canta;
								preciou[longcants]= document.getElementById('cost'+z).value;
								preciot[longcants]= document.getElementById('subtoc'+z).innerHTML;
								cambiapi[longcants]= document.getElementById('cambio'+z).innerHTML;
								longcants++;
							}
						}

						//si todo ok, se envian los datos
						$.post( "php/enviaoc.php",
						{	prov:prov,
							cred:cred,
							tpago:tpago,
							fact:fact,
							cambiatp:cambiatp,
							imps:imps,
							stotal:stotal,
							total:total,
							prods:prods,
							cants:cant,
							preciou:preciou,
							preciot:preciot,
							cambiapi:cambiapi
							
						 }, null, "json" )
    						.done(function( data) {
        						//proceso ok
        						if(data.resultado==0){
        							var noc= data.noc;
        							var arts =data.arts;
        							var total = $.number(data.total,2);
        							let cad = "Numero: "+ noc + "<br>"+"Arts: "+arts+"<br>"+"total: "+total
        							+"<br>";
        							let cambiop=data.cambiop;
        							cambiop==0?cad=cad+"COSTOS ACTUALIZADOS":"";
    								confirma(cad,noc);
    								$( "#confirma" ).on( "popupafterclose", function( event, ui ) {
    									location.reload();
    									$('#ocprov').selectmenu( "enable" );
    								} );
            						}else{
                				//proceso no ok
                						let guion="Error:"+data.mensaje+" codigo:"+data.resultado;
                        				aviso(guion);
                						}
    						})
							.fail(function(xhr,textStatus,errorThrown) {
    								aviso(xhr.responseText+ ""+ errorThrown);
								$( "#aviso" ).on( "popupafterclose", function( event, ui ) {
									//location.reload();
									//$('#ocprov').selectmenu( "enable" );
								} );

							});
					}
				
				}
	
				function metodopc(metodo){
					var respuesta
					//esta funcion convierte el numero de metodo de pago en 
					//texto para anunciarlo en la pagina
						switch (metodo){
						case "1":
							respuesta = "EFECTIVO";
						break;
						case "3":
							respuesta = "TRANSFERENCIA";
						break;
						case "28":
							respuesta = "TARJETA DEBITO";
						break;
						case "4":
							respuesta = "TARJETA CREDITO";
						break;
						}
					return respuesta;
					}

			function mensaje(metpag,tventac){
				var respu =[];
				if(tventac == 0){
					respu.push("RECIBIDA Y PAGADA CON:");
					var metodo = metodopc(metpag)
					respu.push(metodo);
					}else{
						respu.push("RECIBIDA")
						respu.push("")
						}
				
				return respu;
				}

//adicion de escucha a botones.	
	//escucha de seleccion de proveedor
		document.getElementById('ocprov').addEventListener('change', function() {
			//se muestra tabla
			hazvisib(true);
			haztabla();
		 	var facturando= document.getElementById('factp');	
			//desenchufar select
			$( "#ocprov" ).selectmenu( "disable" );
	  });
		  //escucha de boton enviar orden recibida
			document.getElementById('envia').addEventListener('click',enviaoc);
			$("#recibe").click(function(){
				//escucha del boton de recibir la orden
				//validaciones previas al registro
				var resul1 = validaelem("tcant","0");
				//interpretacion de la validacion
				if(resul1== 0){
					responde();
					}else{
						// Si ok validacion, elaboracion de la advertencia
						//ocultar campos de pago cuando la recepcion es a credito
						if($("#tventap :radio:checked").val()==1){
							$(".ocultame").hide();
							}
						var tventa = $("#tventap :radio:checked").val();
						var metodop = $("#tpago :radio:checked").val();
						var trespu = mensaje(metodop,tventa);
						$("#menspago1").append(trespu[0]);
						$("#menspago2").append(trespu[1]);
						$("#adpago").popup("open");
						$("#fecha").focus();
						}
				
			});		

			$("#recibeoc").click(function(){
				//escucha del boton "ok procesar"
				//cerrar el popup
				$("#adpago").popup("close");
					$(".mens").empty();
					if($("#tventap :radio:checked").val()==1){$(".ocultame").show();}
				//primero se da de alta la odc y los articulos como siempre, pero se marca como recibida y/o pagada
				regoc();					
				//evaluacion de resultado
				});
			
			   
			   $("#enviac").click(function(){
			   	//escucha del boton envia correo
			   		var numoc=$("#noc").val();
			   		$.post("php/enviamailoc.php",{oc:numoc},null,"json").done(function(data){
			   			var resul= data;
			   			if(resul==0){
			   				$("#confirma").popup("close");
			   				alert("CORREO ENVIADO");
			   			}else{
			   				$("#confirma").popup("close");
			   				alert("ENVIO FALLIDO");
			   				};
			   		});
			   });
			   
			    $("#noenvia").click(function(){
			   	//escucha del boton no evia correo
			   		$("#confirma").popup("close");
			   		location.reload();
			   		$('#ocprov').selectmenu( "enable" );
			   });

				$("#noenviaoc").click(function(){
					$("#adpago").popup("close");
					$( ".mens" ).empty();
					$(".ocultame").show();
					});
			   
			  $("#cancela").click(function(){
			   		location.reload();
			   		$('#ocprov').selectmenu( "enable" );
			   });

				//escucha de decision tipo de pago
				$('#tventap').find('[type="radio"]').on('click', function( event ){    
      				if ($(this).attr("id") == "rcredcon-a") { hazvisib2(true);} else { hazvisib2(false);}				
  			});	
	  			
			 //escucha de decision facturar
			$('#factp').on('change', function( event ){  
				var titulot;  
				if($('#factp').prop('checked')== true){
					//si facturar, se añaden renglones de iva y total
						titulot =document.getElementById('tsubt');
						titulot.innerHTML="SUBTOTALES";		 	
			 }else{titulot =document.getElementById('tsubt');
						titulot.innerHTML="TOTALES";};		
  			});			

	});
		
	})();	
	</script>
</head>

<body>
  <div data-role="page" id="ocpag"> 
    <div data-role="header">
    <a href="portalmov.php" data-ajax="false" class="ui-btn-left ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-home">Inicio</a>
<h1>Orden De Compra</h1>
    <a href="logout.php" data-ajax="false" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
</div>
    <div class="ui-content">
    	<a href="#navpanel" class="ui-btn ui-shadow ui-corner-all ui-btn-inline ui-btn-icon-left ui-icon-bars">Navegaci&oacute;n</a>
		<form  method="post"
		enctype="application/x-www-form-urlencoded" name="ocforma" id="ocforma">
			<label for="ocprov" class="select">Proveedor</label>
			<select name="ocprov" id="ocprov">
			    <option value="0">Seleccione al proveedor</option>
			</select>
			<div id="ococult" class="tablaoculta">
				<div id="opcionesp" >
					<fieldset id="tventap" name="tventap" data-role="controlgroup" data-type="horizontal">
			    		<legend>Tipo de Venta</legend>
			    		<label>
			    			<input type="radio" id="rcredcon-a" name="rpcredcon" value= "0"/>Contado
			    		</label>
			    		<label>
			    			<input type="radio" id="rcredcon-b" name="rpcredcon" value= "1"checked="checked"/>Crédito
			    		</label>
	    			</fieldset>	
	    			<fieldset id ="tpago" name = "tpago" data-role="controlgroup" data-type="horizontal" class="tablaoculta" >
	    				<legend>Método de Pago</legend>
	    				<label>
			    			<input type="radio" id="rtpago-a" name="rtpago" value= "1" checked="checked"/>Efectivo
			    		</label>
			    		<label>
			    			<input type="radio" id="rtpago-b" name="rtpago" value= "3"/>Transferencia
			    		</label>
			    		<label>
			    			<input type="radio" id="rtpago-c" name="rtpago" value= "28"/>Tarjeta de débito
			    		</label>
	    			</fieldset>
	    			<fieldset>
	    				<div data-role="fieldcontain">
		    				<label for="facturarp">Facturada?</label>
			    			<input type="checkbox" data-role="flipswitch" id="factp" name="factp" 
			    			data-on-text="Si" data-off-text="No" 
			    			data-wrapper-class="custom-label-flipswitch"
			    			checked
			    			/>
	    				</div>	    				
	    			</fieldset>
	    			<fieldset>
	    				<div data-role="fieldcontain">
		    				<label for="cambiap">Actualizar precios?</label>
			    				<input type="checkbox" data-role="flipswitch" id="cambiap" name="cambiap"
			    				data-on-text="Si" data-off-text="No" data-wrapper-class="custom-label-flipswitch"/>
	    				</div>	    				
	    			</fieldset>
	    		</div>
	    		<fieldset class="ui-grid-d" id="octabla">
	    			<div class="ocult">reng</div>
					<div class="ui-block-a"><div class="ui-bar ui-bar-b">NoId</div></div>
				    <div class="ui-block-b"><div class="ui-bar ui-bar-b">Producto</div></div>
				 	<div class="ui-block-c"><div class="ui-bar ui-bar-b">Costo</div></div>
				    <div class="ui-block-d"><div class="ui-bar ui-bar-b">Cantidad</div></div>
				    <div class="ocult">subtotalo</div>
				    <div class="ui-block-e"><div class="ui-bar ui-bar-b">Subtotal</div></div>
				    <div class="ocult">iva</div>
				    <div class="ocult">ieps</div>
				    <div class="ocult">civa</div>
				    <div class="ocult">cieps</div>
				</fieldset>		
				<input data-theme="b" data-icon="check" data-iconshadow="true" value="Enviar a Proveedor" type="button" 
		    	name="envia"id="envia">
		    	<input data-theme="a" data-icon="check" data-iconshadow="true" value="Recibir" type="button" 
		    	name="recibe"id="recibe">
		    		<input data-theme="b" data-icon="delete" data-iconshadow="true" value="Cancelar Envío" type="button" 
		    	name="cancela"id="cancela">
			</div>
		</form>
    </div>
    
<div data-role="popup" id="aviso" >
	<p>Sin texto, todavía.</p>
</div>

<div id="confirma" data-role="popup"  data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:400px;">
    <div data-role="header" data-theme="a">
	    <h1>O. Compra OK.</h1>
	    <h2 id="datos"></h2>
    </div>
    <div role="main" class="ui-content">
        <h3 class="ui-title">¿Enviar Correo al Proveedor?</h3>
	<br>
		<input id="noc" type="hidden" />
        <button id="enviac" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">SI, enviar</button>
        <button id="noenvia" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" >NO</button>
    </div>
</div>

<div id ="adpago" data-role= "popup" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:400px;">
     <div data-role="header" data-theme="b">
     	<p>AVISO:</p>
     </div>
     	<div role="main" class="ui-content">
     	<h6> LA ORDEN SE ANOTARA COMO </h6>
     	<h6 id ="menspago1" class = "mens"></h6>
     	<p id="menspago2" class = "mens"></p>
     	<label for ="fecha" >Fecha:</label>
		<input type="date" name="fecha" id="fecha">
		<label for ="factura" >Factura:</label>
		<input type="text" name="factura" id="factura">
		<label for ="ctapago" class ="ocultame">Cuenta:</label>
		<input type="text" name="ctapago" id="ctapago" class ="ocultame">
     		<button id="recibeoc" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">OK, Procesar</button>
        		<button id="noenviaoc" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" >Cancelar Registro</button>
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
<script src="js/fauxoc.js"></script>
</html>
