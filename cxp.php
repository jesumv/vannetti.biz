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
/** consulta a bd **/
			$table = 'oc';
		 	$table2 = 'proveedores';
			 $sql= "SELECT t2.razon_social,t1.fecharec, t1.idoc,t1.factura,t1.remision,t1.monto,t1.iva,t1.total,t2.diascred,t1.facturar,t1.idproveedores,t1.saldo FROM $table
			 AS t1 INNER JOIN $table2 AS t2 ON t1.idproveedores= t2.idproveedores WHERE t1.status >10 AND t1.status <99 AND t1.credito = 1 ORDER BY fecharec,idoc";
			 $result2 = mysqli_query($mysqli,$sql)or die ("ERROR EN CONSULTA DE CUENTAS POR PAGAR.".mysqli_error($mysqli));;
			 	 if(mysqli_num_rows($result2)) {
		 
  			}
    } else {
        //die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
    
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vanneti Cucina</title>
  <!-- Insert link to styles here -->
   <link rel="stylesheet" type="text/css" href="css/inline.css">
   <link rel="stylesheet" type="text/css" href="css/plant1.css">
   <link rel="stylesheet" type="text/CSS" href="css/dropdown_two.css" />
   <link rel="shortcut icon" href="img/logomin.gif" />  
   <link rel="apple-touch-icon" href="img/logomin.gif">
<script src="js/jquery3/jquery-3.0.0.min.js"></script>
   <script>
   'use strict';
   	(function() { 
   		$(document).ready(function() {
   	   		//variable para guardar saldo del docto eleg
   	   		var saldoi;
   			var app = {
   				    isLoading: true,
   				    spinner: document.querySelector('.loader'),
   				    container: document.querySelector('.main'),
   				    addDialog: document.querySelector('.dialog-container'),
   				  };

   		 //deshabilitar campo saldo
			  $( '#saldo' ).prop( "disabled", true );
		//funciones principales
			  function noc(ped){
					//obtiene el no. pedido
						var longi=ped.length-3;
						var peds=ped.slice(-longi);
						return peds;
					} 
			function mpago(indice){
						//muestra dialogo pago
							//fecha por defecto
		  				document.getElementById("fpago").valueAsDate = new Date();	
						//obtener numero de pedido
						var pedn= noc(indice);
						var texto = "REGISTRO DE PAGO OC "+pedn;
						var fact=document.getElementById('nofact'+pedn).innerHTML;
						var saldoa=document.getElementById('saldot'+pedn).innerHTML;
						saldoi = saldoa;
						document.getElementById('titulod').innerHTML= texto;
						document.getElementById('nfact').value = fact;
						document.getElementById('saldo').value = saldoa;
						document.getElementById('monto').value = saldoa;
						document.getElementById('fpago').focus();
				   		app.toggleAddDialog(true)
				  }

		   	
		   	function resulreg(reng){
				//anuncia el resultado positivo del registro
				//div no. de factura
				var ese= document.getElementById('nofact'+reng)
				//input de factura
				var nofact= document.getElementById('nfact').value
				ese.innerHTML= nofact;
				//no permite el registro de nuevo
				var celda=document.getElementById('celp'+reng)
				var boton= document.getElementById('pag'+reng)
				celda.removeChild(boton)
				celda.style.backgroundColor = "green";
				celda.innerHTML="PAGO REGIST";
				limpia()
			} 

			function valida(indic){
		   		var fecha=document.getElementById('fpago').value;
		   		var factu= document.getElementById('sfact'+indic).innerHTML;
		   		var nfactu=document.getElementById('nfact').value;
		   		var arch= document.getElementById('arch').value;
		   	    var mpag=document.getElementById('smpago').value;
		   	    var ctaact=document.getElementById('cuenta').value;
		   	    var folioact=document.getElementById('folio').value;
		   	    var montop = Number(document.getElementById('monto').value);
		   	    var saldo = Number(document.getElementById('saldo').value);
		   	    //corregir funcion fecha
		   	    var fechac=isValidDate(fecha)
		   		if(!fechac){return -1;}
		   		if(factu==1){
		   				if(nfactu==""){return -2}
		   				if(arch==""){return -3}
		   				if(mpag==0){return -4}else if(mpag>1 && ctaact==""){return -5}
		   				else if(mpag>1 && mpag<4 && folioact==""){return-6};
		   		}
		   		if (montop>saldoi){	return -7}; 
			}
			  
			function epago(){
			 	//esta funcion añade escuchas a botones de pago
			 	var bpagar = document.getElementsByClassName('bpag');
			  	for (var i = 0; i < bpagar.length; i++) {
			  			bpagar[i].addEventListener('click', function(){mpago(this.id)}, false)
			  		}
			}
					
			function evalua(resul,reng){
			/** se evalua la respuesta de enviapagoc.php**/
				var resp;
				switch (resul){
					case -1:
						resp = "ERROR EN CONEXION A BD";
						resulmal(resp);
						break;
					case -2:
						resp = "ERROR EN REGISTRO CONTABLE";
						resulmal(resp);
						break;
					break;
					default:
						resulreg(reng);
						//cerrar dialogo
						app.toggleAddDialog(false);	
				}
			}
			
			function limpia(){
				document.getElementById('avisor').innerHTML='';
				document.getElementById('rpagooc').reset();
			}
			
			function enviapagoc(reng,montop){
				//recoleccion de variables -- de dialogo de pago
				var fecha= document.getElementById("fpago").value;
				var saldof= document.getElementById("saldo").value;
				var factu= document.getElementById("nfact").value;
				var arch= document.getElementById("arch").value;
				if(arch==""){arch="factura pendiente"};
				var metpago= document.getElementById("smpago").value;
				var cta = document.getElementById("cuenta").value;
				var folio=document.getElementById("folio").value;
				//de tabla principal
				var sfact= document.getElementById("sfact"+reng).innerHTML;
				var subt= document.getElementById("subt"+reng).innerHTML;
				var iva= document.getElementById("iva"+reng).innerHTML;
				var saldoi= document.getElementById("saldot"+reng).innerHTML;
				var totalp= document.getElementById("total"+reng).innerHTML;
				var noprov= document.getElementById("noprov"+reng).innerHTML;
	
				//envio a bd
				$.post( "php/enviapagoc.php",
							{	sfact:sfact,
								oc:reng,
								idprov:noprov,	
								fecha:fecha,
								saldoi: saldoi,
								factu:factu,	
								arch:arch,
								metpago:metpago,
								cta:cta,
								folio:folio,
								subt:subt,
								iva:iva,
								total:totalp,
								monto: montop						
							 }, null, "json" )
							 	.done(function(data) {
	    							var resul= data.resul;
	    							var saldofr = data.saldof;
	    							document.getElementById("saldot"+reng).innerHTML= saldofr;
	    							var textor = evalua(resul,reng);
	    						})
	    						.fail(function(xhr, textStatus, errorThrown ) {		
	    							resulmal("error en registro pago: "+xhr.responseText);
								});	
				
				//recepcion de respuesta
				//traduccion
				//fin
			}
		 /*****************************************************************************
		   *
		   * Methods to update/refresh the UI
		   *
		   ****************************************************************************/  
	  
	// Toggles the visibility of dialog.	  	 
			  app.toggleAddDialog = function(visible) {
			    if (visible) {
			      app.addDialog.classList.add('dialog-container--visible');
			    } else {
			      app.addDialog.classList.remove('dialog-container--visible');
			    }
			  }; 
		   
			    if (app.isLoading) {
							      app.spinner.setAttribute('hidden', true);
							      app.container.removeAttribute('hidden');
							      app.isLoading = false;
						    }
			//escuchas de elementos dialogo
						//fecha
				document.getElementById('fpago').addEventListener('change',function(){document.getElementById('nfact').focus()})
						//factura
				document.getElementById('nfact').addEventListener('change',function(){document.getElementById('arch').focus()})			
						//archivo
				document.getElementById('arch').addEventListener('change',function(){document.getElementById('smpago').focus()})
						//metodo de pago
				document.getElementById('smpago').addEventListener('change',function(){
						var mpago=document.getElementById('smpago').value;

        						switch(mpago) {
        					    case "2":
        					    case "3":
        					    		document.getElementById('cuenta').value="8145";
        					    		document.getElementById('folio').focus()
        					        break;
        					    case "28":
        					    	document.getElementById('cuenta').value="2730";
        					    	document.getElementById('folio').focus()
        					        break;
        					    default:
        					    	document.getElementById('regpago').focus()
        					}
				});

						//cuenta
						document.getElementById('cuenta').addEventListener('change',function(){
							document.getElementById('regpago').focus()
						});

						//monto del pago
						document.getElementById('monto').addEventListener('change',function(){
							var pagon = Number(this.value);
							var aviso =document.getElementById('avisor');
							var saldom = Number(document.getElementById('saldo').value);
							if(pagon<=saldom){
								var saldon = saldom - pagon;
								document.getElementById('saldo').value = saldon;
								document.getElementById('nfact').focus();
								aviso.innerHTML="";
								}else{
									document.getElementById('saldo').value = saldom;
									var casilla = this;
									aviso.innerHTML="MONTO DE PAGO NO PUEDE SER MAS QUE SALDO";
									this.value = null;
									this.focus();
									}
							

							});

	
				//escucha de boton cancela
					document.getElementById('butAddCancel').addEventListener('click', function() {
						// oculta el dialogo de datos de producto
							app.toggleAddDialog(false);
						//limpia los datos
							limpia();
					},false)
//valida que al elegir un archivo de factura, este no haya sido elegido previamente.
			 //escucha de boton enviar pago
				document.getElementById('regpago').addEventListener('click',function(){
					var aqui= document.getElementById('avisor');
					aqui.innerHTML="";
					//obtener orden de compra
					var titu=document.getElementById('titulod').innerHTML
					var titul=titu.length-20;
					var noc=titu.slice(-titul);
					//validaciones
					var resulp= valida(noc);
					var montop = document.getElementById('monto').value;
					switch(resulp){					
						case -1:
							//no fecha valida
						aqui.innerHTML="POR FAVOR INTRODUZCA FECHA VALIDA (AAAA/MM/DD)";
						document.getElementById('fpago').focus();
						break;
						case -2:
						aqui.innerHTML="ES NECESARIO EL NUMERO DE FACTURA";
						document.getElementById('nfact').focus();
						break;
						case -4:
						aqui.innerHTML="ELIJA EL MEDIO DE PAGO";
						document.getElementById('smpago').focus();
						break;
						case -5:
						aqui.innerHTML="INTRODUZCA LA CUENTA DE PAGO";
						document.getElementById('cuenta').focus();
						break;
						case -6:
						aqui.innerHTML="INTRODUZCA EL FOLIO OPERACION";
						document.getElementById('folio').focus();
						break;
						case -7:
							aqui.innerHTML="MONTO DE PAGO NO PUEDE SER MAS QUE SALDO";
							document.getElementById('monto').focus();
						break;
						default:
						//enviar a registro
						enviapagoc(noc,montop);
					}
					}); 
		   //anade escuchas a botones pago
		    epago();
		
//end ready    	   	   		
   	   	})	
//end function	
	})();

   </script>
    <script src="js/fauxcx.js"></script>
</head>
<body>

  <header class="header">
  	<div>
    	<h1 class="header__title">Bienvenido(a), <?php echo $_SESSION['nombre']; ?></h1>
    </div>
    
  </header>

  <main class="main">
		 <br/>
		 <h2>CUENTAS POR PAGAR</h2>
		  
		  <?php
		  		include_once "include/menu1.php";
		  ?>
	
	<table id="tblcxp" name="tblcxp" class="db-table">
		<tr><th>PROVEEDOR</th><th>FECHA</th><th>OC</th><th>FACTURA</th><th>REMISION</th><th>MONTO</th><th>IVA</th><th>TOTAL</th><th>SALDO ACT</th><th>DIAS VENC</th><th>PAGO</th></tr>
	
	<?php
	//-----CONSTRUCCION DE LA TABLA------------------------------------------------------------------------
	  if(mysqli_num_rows($result2)) {
	  	 //construir tabla
	  	 while($row2=mysqli_fetch_row($result2)){
	  	 				$rz=$row2[0];
	  	 				$fechamov=date_create($row2[1]);
			 			$nooc=$row2[2];
						$factura= $row2[3];
						$remi=$row2[4];
						$monto=$row2[5];
						$iva=$row2[6];
						$total=$row2[7];
						$saldo=$row2[11];
			 			$diascred=$row2[8];
						$facturar=$row2[9];
						$idprov=$row2[10];
			 			$fechamod=date_format($fechamov,'Y/m/d');
						$calc=diasvenc($row2[1], $diascred);
					 	echo "<tr><td class='ocult' id=noprov".$nooc.">".$idprov."</td><td class='ocult' id=sfact".$nooc.">".$facturar."</td>
					 	<td>".$rz."</td><td>".$fechamod."</td><td>".$nooc."</td><td id=nofact".$nooc.">".$factura."</td><td>".$remi."</td>
					 	<td id=subt".$nooc.">".$monto."</td><td id=iva".$nooc.">".$iva."</td><td id=total".$nooc.">".$total."</td><td id=saldot".$nooc.">".$saldo."</td><td>".$calc."</td>
					 	<td class= 'edac' id=celp".$nooc."><a id=pag".$nooc." class='bpag' href='javascript:void(0);'>
					 	<img src='img/check-black.png' ALT='reg pago'></a></td></tr>";
					 } 
	  }else{echo"<h1>no hay pedidos pendientes de cobro</h1>";}
	?>
	</table>
 </main>
 
 <!-- caja dialogo registro pago -->
  <div class="dialog-container">
    <div class="dialog">
    	<div class="dialog-title" id="titulod"></div>
	    	<div class="dialog-body">
	    		<form id="rpagooc" method ="post" action="#" onsubmit="return false;">
	    			<div class="rengn">
	    			<label>Fecha: </label><input type="date" name="fpago"  id="fpago" class="cajac"/>
	    			<label>Saldo: </label><input type="text" name="saldo"  id="saldo" class="cajac"/>	
	    			<label>Monto: </label><input type="number" name="monto" min="0" step ="any" id="monto" class="cajac"/>	
	    			
	    			</div>
	    			<div class="rengn">
	    			<label>Factura: </label><input type="text" name="nfact"  id="nfact" class="cajam"/>
	    			<label>Archivo XML: </label><input type="file" name="arch"  id="arch" accept=".xml"/>
	    			</div>
	    			<label>Metodo de Pago: </label>
	    			<div class="rengn">
	    				<select id="smpago" name="smpago">
							<option value="0">Seleccione el medio de pago</option>
							<option value="01">Efectivo</option>
							<option value="02">Cheque</option>
							<option value="03">Transferencia</option>
							<option value="04">Tarjetas de Credito</option>
							<option value="28">Tarjetas de Débito</option>
							<option value="99">Otros</option>
         				</select>
         				<label>Cuenta de Pago: </label><input type="text" name="cuenta"  id="cuenta" class="cajac" maxlength="4" />
         				<label>Folio Op: </label><input type="text" name="folio"  id="folio" class="cajac" />
	    			</div>
	    			<div class="rengn">
	    				<h4 id="avisor"></h4>
	    			</div>
	    			<div class="dialog-buttons">
				      	<input type="submit" value="Registrar" id="regpago" class="button a"/>
				      	<button id="butAddCancel" class="button b" style="margin-left: 10px">Cancelar</button>
				    </div>
	    		</form>
	    	</div>
    </div>
  </div>

  <div class="loader">
    <svg viewBox="0 0 32 32" width="32" height="32">
      <circle id="spinner" cx="16" cy="16" r="14" fill="none"></circle>
    </svg>
  </div>
  
  
</body>
</html>

