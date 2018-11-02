<?php
  function __autoload($class){
	  require('include/' . strtolower($class) . '.class.php');
    }
    
//funciones auxiliares
require 'include/funciones.php';
		function sfactura($facti){
		//string si se factura o no el pedido
		if($facti==0){return "NO";}else{return "SI";}
	}
	function estadop($estado){
	//entero a string para texto en tabla
		switch ($estado) {
			case 20:
				$resul="AL COBRO";
				break;
			case 25:
				$resul="X FACTURAR";
				break;
			case 30:
				$resul="X COBRAR";
				break;
			case 35:
			    $resul="PAGO PARCIAL";
			    break;
			default:
				$resul="ERROR";
				break;
		}
		
		return $resul;	
	}
	
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
/*** checa login***/
       $funcbase->checalogin($mysqli);
/** consulta a bd **/
$table = 'pedidos';
$table2 = 'clientes';
 $sql= "SELECT t2.razon_social,t1.fecha, t1.idpedidos,t1.factura,t1.monto,t1.iva,t1.total,
t2.diascred,t1.status,t1.facturar,t1.idclientes,t1.saldo,t1.arch FROM $table AS t1 
INNER JOIN $table2 AS t2 ON t1.idclientes= t2.idclientes WHERE t1.status >19 AND 
t1.status <40 AND (t1.tipovta = 2 OR t1.tipovta=1) ORDER BY t1.fecha";
 $result2 = mysqli_query($mysqli,$sql)or die ("ERROR EN CONSULTA DE CUENTAS POR COBRAR.".mysqli_error($mysqli));; 

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
  <title>CXC</title>
  <!-- Insert link to styles here -->
   <link rel="stylesheet" type="text/css" href="css/inline.css">
   <link rel="stylesheet" type="text/css" href="css/plant1.css">
   <link rel="stylesheet" type="text/CSS" href="css/dropdown_two.css" />
   <link rel="shortcut icon" href="img/logomin.gif" />  
   <link rel="apple-touch-icon" href="img/logomin.gif">
	<script src="js/jquery3/jquery-3.0.0.min.js"></script>
	<script src="js/fcfdi.js"></script>
   <script>
   'use strict';
   	(function() { 	
	   	$(document).ready(function(){
	   		 var app = {
			    isLoading: true,
			    spinner: document.querySelector('.loader'),
			    container: document.querySelector('.main'),
			    addDialog: document.querySelector('.dialog-container'),
			  };

			function sumatots(){
				//suma las columnas de cifras para el renglon de totales
				var subts= document.getElementsByClassName("subto");
				var tiva = document.getElementsByClassName("siva");
				var ttot= document.getElementsByClassName("sutot");
				var tsaldo= document.getElementsByClassName("ssaldo");
				var lsubts= subts.length;
				var sumas = 0;
				var sumaiva =0;
				var sumatot =0;
				var sumasaldo =0;
				for(var i=0; i< lsubts; i++){
						sumas += parseFloat(subts[i].innerHTML);
						sumaiva += parseFloat(tiva[i].innerHTML);
						sumatot += parseFloat(ttot[i].innerHTML);
						sumasaldo += parseFloat(tsaldo[i].innerHTML);
					}
				document.getElementById("tsubt").innerHTML = sumas.toFixed(2)
				document.getElementById("tiva").innerHTML = sumaiva.toFixed(2)
				document.getElementById("ttot").innerHTML = sumatot.toFixed(2)
				document.getElementById("tsaldo").innerHTML = sumasaldo.toFixed(2)
				
				}
			function procfact(e,callback){
				//acciones al elegir un archivo de factura
				var cnfact = document.getElementById("nfact");
				var haynfact= cnfact.value;
				if(haynfact ==""){
					var files = e.target.files; // FileList object
						for (var i=0, f; f=files[i]; i++) {
					          var r = new FileReader();
				            		r.onload = (function(f) {
				                		return function(e) {
				                			var arch= f.name;
				                    		var contents = e.target.result;
				                    		var cfdireg;
				                    			var resul=leeXML(contents,arch);
				                    			if(resul.exito ==0){
					                    			cnfact.value=resul.seriefolio;
				                    			}else{
														alert("error en lectura xml");
				                					
				                    				}
				                    			
				                				};
				           	 		})(f);

				            r.readAsText(f);
				        }
					}	
				document.getElementById('smpago').focus()
				}  
			function nped(ped){
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
				var pedn= nped(indice);
				var texto = "REGISTRO DE PAGO PEDIDO "+pedn;
				document.getElementById('titulod').innerHTML= texto;
				var varsnfact= 'afact'+pedn;
				var saldoa=document.getElementById('saldo'+pedn).innerHTML;
				var fact=document.getElementById('nofact'+pedn).innerHTML;
				var nfact =document.getElementById(varsnfact).value;
				document.getElementById('nfact').value= nfact;
				document.getElementById('saldo').value = saldoa;
				document.getElementById('monto').value = saldoa;
				document.getElementById('fpago').focus();
		   		app.toggleAddDialog(true)
		   	}
		   	
		   	function resulreg(reng){
				//anuncia el resultado positivo del registro
				var este= document.getElementById('stped'+reng)
				var ese= document.getElementById('afact'+reng)
				var nofact= document.getElementById('nfact').value;
				este.innerHTML="PAGO REGISTRADO"
				este.style.backgroundColor = "green";
				if(ese.value==""){
					ese.value= nofact;
					}

				//no permite el registro de nuevo
				var celda=document.getElementById('celp'+reng)
				var boton= document.getElementById('pag'+reng)
				celda.removeChild(boton)
				celda.style.backgroundColor = "#ececec";
				limpia()
				//actualiza totales
				sumatots();
			}		   	
		   	function valida(indic){
		   		var fecha=document.getElementById('fpago').value;
		   		var factu= document.getElementById('facti'+indic).innerHTML;
		   		var nfactu=document.getElementById('nfact').value;
		   		var arch= document.getElementById('arch').value;
		   	    var mpag=document.getElementById('smpago').value;
		   	    var ctaact=document.getElementById('cuenta').value;
		   	    var montop= document.getElementById('monto').value;
		   	 	var saldoi= document.getElementById('monto').value;
		   	    //corregir funcion fecha
		   	    var fechac=isValidDate(fecha)
		   		if(!fechac){return -1;}
		   		if(factu=="SI"){
		   				if(nfactu==""){return -2}
		   				if(arch==""){return -3}
		   				if(mpag==0){return -4}else if(mpag>1 && ctaact==""){return-5}
		   				}else{
		   			if(mpag==0){return-4}else if(mpag>1 && ctaact==""){return-5};
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

			function ifact(elem){
				//habilita no. factura y lo toma
				var lpedid=elem.id.length-6;
					var pedid = elem.id.slice(-lpedid);
					var entra = document.getElementById("afact"+pedid);
					var camstat= document.getElementById("stped"+pedid);
				//habilitar el input
					entra.disabled = false;
				//agregar escucha de change
					entra.addEventListener("change",function(){
						var fact = entra.value;
						var  datos = {pedido:pedid,factura:fact}
						var xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								entra.style.backgroundColor = "green";
								entra.disabled=true;
								camstat.innerHTML = "X COBRAR";
							    }
							}
						xhttp.open("POST", "php/envianofact.php", true);
						xhttp.setRequestHeader("Content-type","application/json");
						xhttp.send(JSON.stringify(datos));
						},false)
					
				}
			function efact(){
				//añade escuchas a td factura
				var factur = document.getElementsByClassName("efact");	
		      	for (var i = 0; i < factur.length; i++) {
	    			factur[i].addEventListener('dblclick',function(){ifact(this)}, false);
					}
				}
	
			function limpia(){
				document.getElementById('avisor').innerHTML='';
				document.getElementById('rpago').reset();
			}
			function evalua(resul,reng){
			/** se evalua la respuesta de enviapago.php**/
				switch (resul){
					case -1:
						resp = "ERROR EN REGISTRO DE ARTICULOS";
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
			
			function enviapago(reng){
				//recoleccion de variables
				var sfact= document.getElementById("sfact"+reng).innerHTML;
				var fecha= document.getElementById("fpago").value;
				var metpago= document.getElementById("smpago").value;
				var subt= document.getElementById("subt"+reng).innerHTML;
				var iva= document.getElementById("iva"+reng).innerHTML;
				var montop= document.getElementById("monto").value;
				var saldoi = document.getElementById("saldo"+reng).innerHTML;
				var saldof= document.getElementById("saldo").value;
				var idcte= document.getElementById("idcte"+reng).innerHTML;
				var arch= document.getElementById("arch").value;
				//recoger si se factura o no
				var factu=document.getElementById("nfact").value;
				//envio a bd
				$.post( "php/enviapagop.php",
							{	sfact:sfact,
								pedido:reng,
								fecha:fecha,
								subt:subt,
								iva:iva,
								saldoi:saldoi,
								saldof:saldof,
								montop:montop,
								metpago:metpago,
								factu:factu,	
								idcte:idcte,
								arch:arch								
							 }, null, "json" )
							 	.done(function( data) {
	    							var resul= data.resul;
	    							var saldofr = data.saldof;
	    							document.getElementById("saldo"+reng).innerHTML= saldofr;
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
				//escuchas de elementos dialog
				document.getElementById('fpago').addEventListener('change',function(){document.getElementById('nfact').focus()})
				document.getElementById('nfact').addEventListener('change',function(){document.getElementById('arch').focus()})
				document.getElementById('arch').addEventListener('change',function(e){
					procfact(e,leeXML),
					false
					})
				document.getElementById('smpago').addEventListener('change',function(){
						var mpago=document.getElementById('smpago').value;
						switch(mpago) {
					    case 1:
					    	document.getElementById('regpago').focus()	
					        break;
					    case 2:
					    case 3:
					    	document.getElementById('cuenta').value= '8145';
					    	document.getElementById('regpago').focus();
					        break;
					    default:
					    	document.getElementById('regpago').focus();
					}
					})
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
				//escucha de boton enviar pago
				document.getElementById('regpago').addEventListener('click',function(){
					var aqui= document.getElementById('avisor');
					aqui.innerHTML="";
				//obtener pedido
					var titu=document.getElementById('titulod').innerHTML
					var titul=titu.length-24;
					var pedi=titu.slice(-titul);
					var resulp= valida(pedi);
					switch(resulp){
						case -1:
							//no fecha valida
						aqui.innerHTML="POR FAVOR INTRODUZCA FECHA VALIDA (AAAA/MM/DD)";
						document.getElementById('fpago').focus();
						break;
						case -2:
						aqui.innerHTML="FALTA NUMERO DE FACTURA";
						document.getElementById('nfact').focus();
						break;
						case -3:
						aqui.innerHTML="POR FAVOR ELIJA UN ARCHIVO XLS";
						document.getElementById('arch').focus();
						break;
						case -4:
						aqui.innerHTML="ELIJA UN METODO DE PAGO";
						document.getElementById('smpago').focus();
						break;
						case -5:
						aqui.innerHTML="FALTAN 4 DIGITOS DE CUENTA";
						document.getElementById('smpago').focus();
						break;
						default:
						//enviar a registro
						enviapago(pedi);
						break;
					}
				})
							
				//anade escuchas a botones pago
			      	epago();
		      	//escuchas a input factura
		      	efact();	
		      	//escucha a monto de pago
		      	document.getElementById('monto').addEventListener('change',function(){document.getElementById('monto').focus()})
		      	//adición de totales
		      	sumatots();
	   		});
   		 		
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
		 <h2>CUENTAS POR COBRAR</h2>
		  
		  <?php
		  		include_once "include/menu1.php";
		  ?>
	
	<table id="tblcte" name= "tblcte" class="db-table">
		<tr><th>FACTURAR</th><th>CLIENTE</th><th>FECHA</th><th>PEDIDO</th>
		<th>FACTURA</th><th>MONTO</th><th>IVA</th><th>TOTAL</th><th>SALDO</th><th>ESTADO</th>
		<th>DIAS VENC</th><th>PAGO</th></tr>
		
	
	<?php
	//-----CONSTRUCCION DE LA TABLA------------------------------------------------------------------------
	  if(mysqli_num_rows($result2)) {
	  	 //construir tabla
	  	 while($row2=mysqli_fetch_row($result2)){
	  	                $razons= $row2[0];
	  	                $fechamov=date_create($row2[1]);
			 			$noped=$row2[2];
			 			$nfact=$row2[3];
			 			$subt=$row2[4];
			 			$iva=$row2[5];
			 			$total=$row2[6];
			 			$diascred=$row2[7];
			 			$fechamod=date_format($fechamov,'Y/m/d');
						$calc=diasvenc($row2[1], $diascred);
						$stped=estadop($row2[8]);
						$sfact=$row2[9];
						$idcte=$row2[10];
						$facti=sfactura($sfact);
						$saldo=$row2[11];
						$arch= $row2[12];
					 	echo "<tr><td id=idcte".$noped." class='ocult'>$idcte</td>
                        <td id=sfact".$noped." class='ocult'>$sfact</td><td id=arch".$noped." class='ocult'>$arch</td>
					 	<td id=facti".$noped.">$facti</td><td>$razons</td><td>$fechamod</td><td id=ped".$noped.">$noped</td>
                        <td class= 'efact' id=nofact".$noped."><input disabled id=afact".$noped." value='$nfact'></td>
					 	<td class='subto' id=subt".$noped.">$subt</td><td class='siva' id=iva".$noped.">$iva</td>
                        <td  class='sutot' id=total".$noped.">$total</td><td class='ssaldo' id=saldo".$noped.">$saldo</td>
                        <td id=stped".$noped.">$stped</td><td>".$calc."</td><td class= 'edac' id=celp".$noped."><a id=pag".$noped." class='bpag' href='javascript:void(0);'>
					 	<img src='img/check-black.png' ALT='reg pago'></a></td></tr>";
					 } 
		echo "<tr class='treng'><td colspan='5'>TOTALES</td><td id='tsubt'></td><td id='tiva'></td>
               <td id='ttot'></td><td id='tsaldo'></td><td colspan='3'></td></tr>";
		
	  }else{echo"<h1>no hay pedidos pendientes de cobro</h1>";}
	?>
	</table>
 </main>
 <!-- caja dialogo registro pago -->
  <div class="dialog-container">
    <div class="dialog">
    	<div class="dialog-title" id="titulod"></div>
	    	<div class="dialog-body">
	    		<form id="rpago" method ="post" action="#" onsubmit="return false;">
	    			<div class="rengn">
	    			<label>Fecha: </label><input type="date" name="fpago"  id="fpago" class="cajam"/>
	    			<label>Factura: </label><input type="text" name="nfact"  id="nfact" class="cajam"/>	
	    			</div>
	    			<div class="rengn">
	    				<label>Saldo: </label><input type="text" name="saldo"  id="saldo" class="cajac" disabled/>	
	    				<label>Monto: </label><input type="number" name="monto" min="0" step ="any" id="monto" class="cajac"/>
	    			</div>
	    			<div class="rengn">
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
							<option value="99">Otros</option>
         				</select>
         				<label>Cuenta: </label><input type="text" name="cuenta"  id="cuenta" class="cajac" maxlength="4"/>
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

