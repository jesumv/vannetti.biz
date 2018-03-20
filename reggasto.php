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
    
function consultad($mysqli){	
//esta funcion crea la tabla con los ultimos 10 movimientos de diario
$consulta="SELECT iddiario,fecha,cuenta,subcuenta,referencia,aux,debe,haber,facturar,coment,arch FROM diario ORDER BY iddiario DESC LIMIT 10";
$query= mysqli_query($mysqli, $consulta) or die ("ERROR EN CONSULTA ULTIMOS MOVTOS. ".mysqli_error($mysqli));
		echo"<DIV style='width:80%'><h3>ULTIMOS DIEZ MOVIMIENTOS DE DIARIO</h3><table border='1' cellspacing='5' cellpadding='5'";
		echo"<tr><th>iddiario</th><th>fecha</th><th>cuenta</th><th>subcuenta</th><th>refer</th><th>aux</th><th>debe</th>
		<th>haber</th><th>fact</th><th>coment</th><th>archivo</th></tr>";
		while ($fila = mysqli_fetch_array($query)) {
				echo"<tr>";
					for ($i=0; $i < 11; $i++) {
							echo "<td>".$fila[$i]."</td>";		 	
					}
				echo"</tr>";
		}
		echo "</table></DIV>";
}

function saldobanco($mysqli){
	//esta funcion presenta el saldo de bancos
	$consulta1=$mysqli->query("SELECT SUM(CASE WHEN cuenta='102.01' THEN debe ELSE 0 END)FROM DIARIO");
	$debe=$consulta1->fetch_row();
	$rdebe=$debe[0];
	$consulta2=$mysqli->query("SELECT SUM(CASE WHEN cuenta='102.01' THEN haber ELSE 0 END)FROM DIARIO");
	$haber=$consulta2->fetch_row();
	$rhaber=$haber[0];
	$bancos=number_format(($rdebe-$rhaber),2);
	//caja
	$consulta3=$mysqli->query("SELECT SUM(CASE WHEN cuenta='101.01' THEN debe ELSE 0 END)FROM DIARIO");
	$debe3=$consulta3->fetch_row();
	$rdebe3=$debe3[0];
	$consulta4=$mysqli->query("SELECT SUM(CASE WHEN cuenta='101.01' THEN haber ELSE 0 END)FROM DIARIO");
	$haber4=$consulta4->fetch_row();
	$rhaber4=$haber4[0];
	$caja=number_format(($rdebe3-$rhaber4),2);
	
	
	echo "<table border='2'>
				<tr><th>SALDO EN BANCOS:</th><th>".$bancos."</th><th>___</th><th>SALDO EN CAJA:</th><th>".$caja."</th></tr>
		</table>";
}
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">

 <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vanneti Cucina</title>
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
		//bandera que indica si se ha modificado un campo manualmente
		var bandera = 0;
		var app=[];

		function llenaforma(fecha,subtotal="",iva="",total="",factura="",concor=""){
			//llena los campos de la forma con datos xml
			var nfecha = new Date(fecha).toISOString().slice(0,10)
			var forma ={
				nf:document.querySelector('#nfact'),
				fg:document.querySelector('#fgas'),
				mg:document.querySelector('#montog'),
				iva:document.querySelector('#ivag'),
				tg:document.querySelector('#totalg'),
				cg:document.querySelector('#concepo'),
				ctg:document.querySelector('#catg'),
			}
			var f = forma;
			f.nf.value =factura;
			f.nf.disabled = true;
			f.fg.value = nfecha;
			f.fg.disabled = true;
			f.mg.value = subtotal;
			f.mg.disabled = true;
			f.iva.value = iva;
			f.iva.disabled = true;
			f.tg.value = total;
			f.tg.disabled = true;
			f.cg.value = concor;
			f.cg.disabled = true;
			f.ctg.focus();

		}
		function resetea(){
			  //limpia la forma
			document.getElementById('avisor').innerHTML="";
 			document.getElementById('rgasto').reset();
 			document.getElementById("mensaje").value = "";
 			var mensac = document.getElementById("mensd");
 			mensac.setAttribute('class', 'ocult');
		  }
		  
   		 $(document).ready(function() {

   			var evt = document.getElementById('arch');
			  evt.addEventListener('change', function(e){listafacturas(e,leeXML)},false);
			  
   		 	var app = {
			    isLoading: true,
			    spinner: document.querySelector('.loader'),
			    container: document.querySelector('.main'),
			    addDialog: document.querySelector('#dialogog'),
			    addDialog2:document.querySelector('#dialogot'),
			  };
			  /*****************************************************************************
		   *
		   * Metodos para actualizar/refrescar la IU
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
			  
			  app.toggleAddDialog2 = function(visible) {
			    if (visible) {
			      app.addDialog2.classList.add('dialog-container--visible');
			    } else {
			      app.addDialog2.classList.remove('dialog-container--visible');
			    }
			  };
			  
			  	   
			    if (app.isLoading) {
							      app.spinner.setAttribute('hidden', true);
							      app.container.removeAttribute('hidden');
							      app.isLoading = false;
						    }
			//metodos de los elementos de la pagina
				function listafacturas(e,callback){
					//obtiene arreglo de elementos de caja de lista y los lee como xml
					var files = e.target.files; // FileList object
					var resul=[];
						for (var i=0, f; f=files[i]; i++) {
					          var r = new FileReader();
				            		r.onload = (function(f) {
				                		return function(e) {
				                			var arch= f.name;
				                    		var contents = e.target.result;
				                    		var cfdireg;
				                    		if(bandera == 0){
				                    			var resul=leeXML(contents,arch);
				                    			if(resul.exito ==0){
				                    				llenaforma(resul.fecha,resul.stotal,resul.iva,resul.total,
				                    				resul.seriefolio,resul.conceptoc)
				                    			}else{
				                    				var mensa = document.getElementById("mensaje");
				                					mensa.value= resul.error;
				                					var mensac = document.getElementById("mensd");
				                					mensac.classList.remove("ocult");
				                					var fecha = new Date();
				                					llenaforma(fecha)
				                					
				                    				}
				                    			
				                    		}else{document.getElementById("catg").focus();}
				                				};
				           	 		})(f);

				            r.readAsText(f);
				        } 
			      	
					}
			function valida(elemen){
		   		var fecha=document.getElementById(elemen).value;
		   	    //corregir funcion fecha
		   	    var fechac=isValidDate(fecha)
		   		if(!fechac){return -1;}else{return 0;}
		   	}
			function muestrad(){
   				app.toggleAddDialog(true)
   				document.getElementById('fgas').focus();
   			}
   			function cancela(){
   				app.toggleAddDialog(false)
   				resetea();
   			}
   			
   			function cancelat(){
   				app.toggleAddDialog2(false)
   				document.getElementById('avisor').innerHTML="";
   				document.getElementById('rtraspaso').reset();
   			}
   			
   			function muestrat(){
   				app.toggleAddDialog2(true)
   				document.getElementById('ftras').focus();
   			}
   				 			
   			function enviagas(){
   				//envio de gasto a la base de datos
   				//recoleccion de variables
   				var fecha = document.getElementById('fgas').value;
   				var monto =	document.getElementById('montog').value;
   				var iva =	document.getElementById('ivag').value;
   				var fact =	document.getElementById('nfact').value;
   				var arch =	document.getElementById('arch').value;
   				var catg =	document.getElementById('catg').value;
   				var concepg =	document.getElementById('concepg').value;
   				var mpago = document.getElementById('smpago').value;
   				var cuenta = document.getElementById('cuenta').value;
   				var folio = document.getElementById('folio').value;
   				var tipo= "g";
   				//envio a bd
   					$.post( "php/enviaotros.php",
							{	tipo:tipo,
								fecha:fecha,
								monto:monto,
								iva:iva,
								fact:fact,
								arch:arch,
								catg:catg,
								concep:concepg,
								mpago:mpago,
								cuenta:cuenta,
								folio:folio,
								orig:"",
								dest:""								
							 }, null, "json" )
							 .done(function(data) {
	    							var resul= data.resul;
	    							document.getElementById('rgasto').reset();
									app.toggleAddDialog(false);
									location.reload(true);
	    						})
	    						.fail(function(xhr, textStatus, errorThrown ) {		
	    							document.write("ERROR EN REGISTRO:"+errorThrown);
								});	
   				
   			}
   			
   			function enviatras(){
   				//envio de traspaso a la base de datos
   				//recoleccion de variables
   				var fecha = document.getElementById('ftras').value;
   				var monto =	document.getElementById('montot').value;
   				var origen =document.getElementById('origent').value;
   				var destino =document.getElementById('destinot').value;
				var concept =document.getElementById('concept').value;
   				var tipo= "t";
   				$.post( "php/enviaotros.php",
							{	tipo:tipo,
								fecha:fecha,
								monto:monto,
								iva:"",
								fact:"",
								arch:"",
								catg:"",
								concep:concept,
								mpago:"",
								cuenta:"",
								folio:"",
								orig:origen,
								dest:destino					
							 }, null, "json" )
							 .done(function(data) {
	    							var resul= data.resul;
									app.toggleAddDialog2(false);
									location.reload(true);
	    						})
	    						.fail(function(xhr, textStatus, errorThrown ) {		
	    							document.write("ERROR EN REGISTRO");
								});	
   			}
   			function regg(){
   				//funcion al apretar el boton de registrar gasto
   				var aqui= document.getElementById('avisor');
				aqui.innerHTML="";
   				var fech1=document.getElementById("fgas").id;
   				var valgas=valida(fech1);
   				switch(valgas){
   					case -1:
   					aqui.innerHTML="POR FAVOR INTRODUZCA FECHA VALIDA (AAAA/MM/DD)";
   					break;
   					default:
   					enviagas();
   					
   				}
   			}
   			function regt(){
   				//al accionar boton de enviar traspaso
   				var aqui= document.getElementById('avisort');
				aqui.innerHTML="";
   				var fech2=document.getElementById("ftras").id;
   				var valtras=valida(fech2);
   				switch(valtras){
   					case -1:
   					aqui.innerHTML="POR FAVOR INTRODUZCA FECHA VALIDA (AAAA/MM/DD)";
   					break;
   					default:
   					enviatras();
   				}
   			}
   			
   			
   			
   		 function calciva(){
				var valor=document.getElementById("montog").value;
				var ivac=valor*.16;
				var civa=document.getElementById("ivag");
				civa.value= ivac.toFixed(2);
				calctotal();
				civa.focus();
				bandera = 1;
			}

   		function calctotal(){
				var base = document.getElementById("montog").value;
				var iva = document.getElementById("ivag").value;
				var total = Number(base) + Number(iva);
				var ctotal = document.getElementById("totalg");
				ctotal.value = total.toFixed(2);
				document.getElementById("catg").focus();
			}
   			
   			function cuentasi(){
   				//esta funcion pone el numero de cuenta default
   				var cuenta = document.getElementById("cuenta");
   				var elec = document.getElementById("smpago").value;
   				switch(elec){
   				case "02":
   				case "03":
   					cuenta.value='8145';
   				break;
   				case "04":
   					cuenta.value='8886';
   				break;
   				case "28":
   				cuenta.value='2730';
   				break;
   				}
   				cuenta.focus();
   			}
   			//escuchas
   			//boton gasto
			document.getElementById("botonp").addEventListener('click',muestrad,false)
			//boton traspaso
			document.getElementById("botont").addEventListener('click',muestrat,false)
			//boton registro gast
			document.getElementById("reggasto").addEventListener('click',regg,false)
			//boton cancela
			document.getElementById("butAddCancel").addEventListener('click',cancela,false)
			//boton registro traspaso
			document.getElementById("regtras").addEventListener('click',regt,false)
			//boton cancela traspaso
			document.getElementById("cancelt").addEventListener('click',cancelat,false)
			//calculo de iva
			document.getElementById("montog").addEventListener('change',calciva,false)
			//calculo de total
			document.getElementById("ivag").addEventListener('change',calctotal,false)
			//metodo de pago
			document.getElementById("smpago").addEventListener('change',cuentasi,false)
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
				 <h2>REGISTRO DE GASTOS Y OTROS</h2>
				  
				  <?php
				  		include_once "include/menu1.php";
				  ?>
				  <br/>
				  <div class="botoncent">
				  	<button class="button c" type="button" id="botonp">Registrar Gasto</button>
				  </div>
				  <br />
				  <div class="botoncent">
				  	<button class="button c" type="button" id="botont">Traspasos</button>
				  </div>
				  <br />
				   <?php
				   saldobanco($mysqli);
				  	consultad($mysqli);
				  ?>
		 </main>
		 <!-- caja dialogo registro pago -->
		 
		  <div class="dialog-container" id="dialogog">
		    <div class="dialog">
		    	<div class="dialog-title" id="titulod">REGISTRO DE GASTO</div>
		    	 	<div id="mensd" class="rengn ocult">
		    			<label>MENSAJES: </label>
		    			<textarea name="mensaje"  id="mensaje" rows="4" cols="50"></textarea>
		    		</div>
			    	<div class="dialog-body">
			    					    		<form id="rgasto" method ="post" action="#" onsubmit="return false;">
			    			<div class="rengn">
						    	<label>Archivo XML: </label><input type="file" name="arch"  id="arch" accept=".xml"/>
						    	<label>Factura: </label><input type="text" name="nfact"  id="nfact" class="cajamfc"/>
						    	<label>Fecha: </label><input type="date" name="fgas"  id="fgas" class="cajacfc"/>
					    	</div>
			    			<div class="rengn">
			    			<label>Subtotal:</label><input type="text" name="montog" id="montog"/>
			    			<label>Iva:</label><input type="text" name="ivag" id="ivag" size="10" class="cajacfc"/>
			    			<label>TOTAL:</label><input type="text" name="totalg" id="totalg" disabled="TRUE"/>
			    			</div>
			    			<div	 class="rengn">
			    			<label>Concepto Orig: </label><input type="text" name="concepo"  id="concepo" class="cajalfc"/>
			    			</div>
			    			<div class="rengn">
			    				<label>Categoría: </label>
			    				<select id="catg" name="catg">
									<option value="0">Seleccione la clase de gasto</option>
									<option value="01">Gastos Generales</option>
									<option value="02">Gastos de Venta</option>
									<option value="03">Gastos de Administración</option>
									<option value="04">Gastos Financieros</option>
									<option value="05">Reembolso de Gastos</option>
									<option value="06">Generales No Deduc</option>
									<option value="07">Ventas No Deduc</option>
									<option value="08">Admon No Deduc</option>
									<option value="99">Otros Gastos</option>
		         				</select>
		         				<label>Concepto: </label><input type="text" name="concepg"  id="concepg" class="cajam" maxlength="20"/>		
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
		         				<label>Cuenta: </label><input type="text" name="cuenta"  id="cuenta" class="cajac" maxlength="4" />
		         				<label>Folio Op: </label><input type="text" name="folio"  id="folio" class="cajac" />
			    			</div>
			    			<div class="rengn">
			    				<h4 id="avisor"></h4>
			    			</div>
			    			<div class="dialog-buttons">
			    				<button type="submit" id="reggasto" class="button a">Registrar</button>
						      	<button type="submit" id="butAddCancel" class="button b" >Cancelar</button>
						    </div>
			    		</form>
			    	</div>
		    </div>
		  </div>
		  
		  <!-- caja dialogo registro traspaso -->
		  <div class="dialog-container" id="dialogot">
		    <div class="dialog">
		    	<div class="dialog-title" id="titulot">REGISTRO DE TRASPASO</div>
			    	<div class="dialog-body">
			    		<form id="rtraspaso" method ="post" action="#" onsubmit="return false;">
			    			<div class="rengn">
			    			<label>Fecha: </label><input type="date" name="ftras"  id="ftras" class="cajam"/>
			    			<label>Monto:</label><input type="text" name="montot" id="montot"/>
			    			</div>
			    			<div class="rengn">
			    				<label>De la Cuenta: </label>
			    				<select id="origent" name="origent">
									<option value="0">Seleccione la cuenta origen</option>
									<option value="101.01">Caja</option>
									<option value="102.01">Banorte</option>
		         				</select>
		         				<label>A la Cuenta: </label>
			    				<select id="destinot" name="destinot">
									<option value="0">Seleccione la cuenta destino</option>
									<option value="101.01">Caja</option>
									<option value="102.01">Banorte</option>
		         				</select>		
			    				
			    			</div>
			    			<div class="rengn">
			    				<label>Concepto: </label><input type="text" name="concept"  id="concept" class="cajam" maxlength="20"/>
			    			</div>
			    			<div class="rengn">
			    				<h4 id="avisort"></h4>
			    			</div>
			    			<div class="dialog-buttons">
			    				<button type="submit" id="regtras" class="button a">Registrar</button>
						      	<button type="submit" id="cancelt" class="button b" >Cancelar</button>
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
      	<footer>
  		</footer>
	</body>
</html>
