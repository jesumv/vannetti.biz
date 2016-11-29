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
		$( document ).on( "pageinit", "#ocpag", function( event ) {
			//traer funciones auxiliares
			 $.getScript( "js/fauxoc.js");
			//modificacion de titulos del flip 
			$( "#factp" ).flipswitch({
	  				onText: "Si",
	  				offText:"No"
				});
			//eventos para los botones de accion
				$("#envia").click(function(){
					var resul1 = validaelem("tcant","0");
			//si no hay datos que enviar
					if(resul1== 0){
						aviso("No ha solicitado ningun articulo");
						$( "#aviso" ).on( "popupafterclose", function( event, ui ) {
								if($("#cant0")){$("#cant0").focus();}
							} );
						}else{
							//recoleccion de variables
							var prov = $("#ocprov").val();
							var total =$("#subtotalo").text();
							var longi = $(".cant").length;
							var prods=[];
							var cant=[];
							var preciou=[];
							var preciot=[];
							var longcants = 0;
							for(var z=0; z <longi; z++){
								if($("#cant".concat(z)).val()!==""){
									prods[longcants] = $("#id".concat(z)).text();
									cant[longcants] = $("#cant".concat(z)).val();
									preciou[longcants]= $("#costo".concat(z)).text();
									preciot[longcants]= $("#subtoc".concat(z)).text();
									longcants++;
								}
							}
							longcants--;
							//si todo ok, se envian los datos
							$.post( "php/enviaoc.php",
							{	prov:prov,
								longi:longcants,
								prods:prods,
								cants:cant,
								preciou:preciou,
								preciot:preciot,
								total:total
							 }, null, "json" )
	    						.done(function( data) {
	    							var noc= data.noc;
	    							var arts =data.arts;
	    							var total = $.number(data.total,2);
	    							var cad = "Numero: "+ noc + "<br>"+"Arts: "+arts+"<br>"+"total: "+total;
									confirma(cad,noc);
									$( "#confirma" ).on( "popupafterclose", function( event, ui ) {
										location.reload();
										$('#ocprov').selectmenu( "enable" );
									} );
	    						})
								.fail( function(xhr, textStatus, errorThrown) {
        							aviso(xhr.responseText);
    							});
						}
			  		
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
			   
			  $("#cancela").click(function(){
			   		location.reload();
			   		$('#ocprov').selectmenu( "enable" );
			   });
			 //escucha de decision facturar
			 document.getElementById('factp').addEventListener('click',function(){
			 	var facturaroc = $("#factp").prop("checked");
				var titulotot;
				if(facturaroc== true){
					//si facturar, se añaden renglones de iva y total
						var titulot =document.getElementById('tsubt');
						titulot.innerHTML="SUBTOTALES";		 	
			 }else{var titulot =document.getElementById('tsubt');
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
				<div id="opcionesp" data-role= "controlgroup" >
					<fieldset id="tventap" name="tventap" data-role="controlgroup" data-type="horizontal">
			    		<legend>Tipo de Venta</legend>
			    		<label>
			    			<input type="radio" id="rcredcon-a" name="rpcredcon" value= "0"/>Contado
			    		</label>
			    		<label>
			    			<input type="radio" id="rcredcon-b" name="rpcredcon" value= "1"checked="checked"/>Crédito
			    		</label>
	    			</fieldset>	
	    			<fieldset>
	    				<div data-role="fieldcontain">
		    				<label >
			    				<input type="checkbox" data-role="flipswitch" id="factp" name="factp"/>Facturada?
			    			</label>
	    				</div>	    				
	    			</fieldset>
	    		</div>
	    		<fieldset class="ui-grid-b" id="octabla">
					<div class="ocult">id</div>
				    <div class="ui-block-a"><div class="ui-bar ui-bar-b">Producto</div></div>
				 	<div class="ocult">Costo</div>
				    <div class="ui-block-b"><div class="ui-bar ui-bar-b">Cantidad</div></div>
				    <div class="ui-block-c"><div class="ui-bar ui-bar-b">Subtotal</div></div>
				    <div class="ocult">subtotalo</div>
				</fieldset>		
				<input data-theme="b" data-icon="check" data-iconshadow="true" value="Enviar" type="button" 
		    	name="envia"id="envia">
		    	<div class="ui-input-btn ui-btn ui-btn-b ui-icon-delete ui-btn-icon-left ui-shadow-icon" 
		    	name="cancela"id="cancela">
		        	Cancelar
		    		<input data-enhanced="true" value="Enhanced" type="button">
		    	</div>	
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
