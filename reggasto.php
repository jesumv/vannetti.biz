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
	$consulta1=$mysqli->query("SELECT SUM(CASE WHEN cuenta='102.01' THEN debe ELSE 0 END)FROM ventas.diario");
	$debe=$consulta1->fetch_row();
	$rdebe=$debe[0];
	$consulta2=$mysqli->query("SELECT SUM(CASE WHEN cuenta='102.01' THEN haber ELSE 0 END)FROM ventas.diario");
	$haber=$consulta2->fetch_row();
	$rhaber=$haber[0];
	$bancos=number_format(($rdebe-$rhaber),2);
	//caja
	$consulta3=$mysqli->query("SELECT SUM(CASE WHEN cuenta='101.01' THEN debe ELSE 0 END)FROM ventas.diario");
	$debe3=$consulta3->fetch_row();
	$rdebe3=$debe3[0];
	$consulta4=$mysqli->query("SELECT SUM(CASE WHEN cuenta='101.01' THEN haber ELSE 0 END)FROM ventas.diario");
	$haber4=$consulta4->fetch_row();
	$rhaber4=$haber4[0];
	$caja=number_format(($rdebe3-$rhaber4),2);
	//ventas
	$consulta5=$mysqli->query("SELECT SUM(haber)FROM diario WHERE MONTH(fecha)=MONTH(NOW())
    AND YEAR(fecha)=YEAR(NOW()) AND CUENTA like('4%');");
	$ventas1=$consulta5->fetch_row();
	$ventas2 = number_format($ventas1[0],2);
	
	//gastos deducibles
	$consulta6=$mysqli->query("SELECT SUM(debe) from diario WHERE (cuenta LIKE('6%') or cuenta LIKE('7%'))
    and cuenta not like('%.8%') AND MONTH(fecha)=MONTH(NOW()) AND YEAR(fecha)=YEAR(NOW())");
	$gastos1=$consulta6->fetch_row();
	$gastos2 = number_format($gastos1[0],2);
	echo "<table border='2'>
<tr>
<th>SALDO EN BANCOS:</th><th>".$bancos."</th><th>___</th><th>SALDO EN CAJA:</th><th>".$caja."</th>
<th>___</th><th>VENTAS DEL MES:</th><th>".$ventas2."</th><th>___</th><th>GASTOS DEL MES:</th><th>".$gastos2."</th>
</tr>
		</table>";
}
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ADMON</title>
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
		var app2=[];
		var ctas=[];
		var obj1;


		//DEFINICION DE FUNCIONES VARIAS
				
		function ivaaux(){
			//calcula el iva de auxiliares
		var iva=document.getElementById("miva");
		var llevaiva= document.getElementById("smpago").value;
		//solo si el met pago es transferencia
		if(llevaiva=="03"){
			var monto= document.getElementById("mprop").value;
			iva.value = monto *0.16;
			}else{iva.value=""}		
		}

		function mconcep(){
		//se evalua el metodo de pago que se introduce
			aparece();
			var metodo = document.getElementById("smpago").focus();
			}
	function aparece(){                              
			//elige elementos auxiliares a presentar
			var concep = document.getElementById("concepg").value;
			var metodo = document.getElementById("smpago").value;
			var etiq =document.getElementById("ladic");
			var propi=document.getElementById("mprop");
		var etiq2 =document.getElementById("lefec");
		var efec =document.getElementById("efec");
		var etiq3 =document.getElementById("liva");
		var miva= document.getElementById("miva");
		var estaoc=etiq.classList.contains("ocult");
			//si se elige alim  y no es transferencia lleva propina
		if(concep=="alim viaje" && estaoc==true){
			etiq.innerHTML="Propina?:";
			etiq.classList.remove("ocult");
			propi.value="";	
			propi.classList.toggle("ocult");
			etiq2.classList.toggle("ocult");	
			efec.classList.toggle("ocult");	
		//si se elige transferencia lleva comision
			}else if(metodo=="03"){
				etiq.innerHTML="Comisión?:";
				propi.value="5";
				ivaaux();
				if(estaoc==true){
					etiq.classList.toggle("ocult");					
					propi.classList.toggle("ocult");
					etiq3.classList.toggle("ocult");
					miva.classList.toggle("ocult");
					}else{

						}											
			}else if(metodo!="03" && concep!="alim viaje"){
				//sie el metodo de pago es distinto, se ocultan las casillas
						etiq.classList.add("ocult");
						propi.value="";					
						propi.classList.add("ocult");
						etiq2.classList.add("ocult");
						efec.classList.add("ocult");
						etiq3.classList.add("ocult");
						miva.classList.add("ocult");
			}				
		}
	
	function modmpago(){
		//esta funcion modifica elementos al cambiar el metodo de pago
		//elige la cuenta a afectar segun el metodo de pago
		cuentasi();
		aparece();
		}
	
		function llenactas(cuentas,elem){
			//autorelleno de select cuentas
				var options = '<option value= "0">Seleccione</option>';
			      for (var i = 0; i < cuentas.length; i++) {
			        options += '<option value="' + cuentas[i].numcta + '">' + cuentas[i].numcta+" "
			        +obj1[i].descri + '</option>';
			      }
			      elem.innerHTML=options;
			}
		function cuentasi(){
			//esta funcion pone el numero de cuenta default
			var cuenta = document.getElementById("cuenta");
			var elec = document.getElementById("smpago").value;
			switch(elec){
			case "02":
			case "03":
			case "13":
				cuenta.value='8145';
			break;
			case "04":
				cuenta.value='8886';
			break;
			case "28":
			cuenta.value='5782';
			break;
			}
			cuenta.focus();
		}

		function llenaforma(fecha,fpago,uuid,subtotal="",iva="",total="",factura="",concor=""){
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
				fpag:document.querySelector('#smpago'),
				uuid:document.querySelector('#uuid')
			}
			var f = forma;
			f.nf.value =factura;
			f.nf.disabled = true;
			f.fg.value = nfecha;
			f.mg.value = subtotal;
			f.iva.value = iva;
			f.tg.value = total;
			f.cg.value = concor;
			f.cg.disabled = true;
			f.fpag.value= fpago;
			f.uuid.innerHTML=uuid;
			cuentasi();
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


		//traer las cuentas para los combos
		$.get('php/getctasat.php', function(data){
			obj1 = JSON.parse(data);	      
	    }).done(function(){
	   		 $(document).ready(function() {
	 			
	    		var evt = document.getElementById('arch');
	 			  evt.addEventListener('change', function(e){tomafactura(e,leeXML)},false);
	 			  
	    		 	var app = {
	 			    isLoading: true,
	 			    spinner: document.querySelector('.loader'),
	 			    container: document.querySelector('.main'),
	 			    addDialog: document.querySelector('#dialogog'),
	 			    addDialog2:document.querySelector('#dialogot'),
	 			  };

	 			  var app2= {
	 					  isLoading: true,
	 					  spinner: document.querySelector('.loader'),
	 					  container: document.querySelector('.main'),
	 					  addDialog: document.querySelector('#dialogoo'),
	 					  }

	 			  var contenedor = document.getElementById('tablamovtos');
	 			  /*****************************************************************************
	 		   *
	 		   * Metodos para actualizar/refrescar la IU
	 		   *
	 		   ****************************************************************************/

	 		   //preparacion de fecha = hoy por defecto
	 		   Date.prototype.toDateInputValue = (function() {
	 			    var local = new Date(this);
	 			    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
	 			    return local.toJSON().slice(0,10);
	 			});

	 			document.getElementById("fechao").value = new Date().toDateInputValue();
	 			document.getElementById("ftras").value = new Date().toDateInputValue();
	 			
	 		   // Toggles the visibility of dialog.	  	 
	 			  app.toggleAddDialog = function(visible) {
	 			    if (visible) {
	 			      app.addDialog.classList.add('dialog-container--visible');
	 			    } else {
	 			      app.addDialog.classList.remove('dialog-container--visible');
	 			    }
	 			  }; 
	 		//muestra el cuadro de otros movs
	 			  app2.toggleAddDialog = function(visible) {
	 				    if (visible) {
	 				      app2.addDialog.classList.add('dialog-container--visible');
	 				    } else {
	 				      app2.addDialog.classList.remove('dialog-container--visible');
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

	 			    if (app2.isLoading) {
	 				      app.spinner.setAttribute('hidden', true);
	 				      app.container.removeAttribute('hidden');
	 				      app.isLoading = false;
	 			    }
	 			    
	 			//metodos de los elementos de la pagina
	 		function tomafactura(e,callback){
	 					//obtiene el elemento seleccionado  de caja de lista y lo lee como xml
	 					var files = e.target.files; // FileList object
	 					var resul=[];
	 					var f=files[0];
	 					var r = new FileReader();
 				        	r.onload = (function(f) {
 				                		return function(e) {
 				                			var arch= f.name;
 				                    		var contents = e.target.result;
 				                    		var cfdireg;
 				                    		if(bandera == 0){
 				                    			var resul=leeXML(contents,arch);
 				                    			if(resul.exito ==0){
 				                    				llenaforma(resul.fecha,resul.fpago,resul.uuid,resul.stotal,resul.iva,resul.total,
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
				
	 			function valida(elemen){
	 		   		var fecha=document.getElementById(elemen).value;
	 		   	    //corregir funcion fecha
	 		   	    var fechac=isValidDate(fecha)
	 		   		if(!fechac){return -1;}else{return 0;}
	 		   	}
	 			function muestrad(){
	 				 var adics=document.getElementsByClassName("adic");
	 				    var longi=adics.length;
	 				    for(var i=0;i<longi;i++){
		 				    	adics[i].classList.add("ocult");
		 				    }
	    				app.toggleAddDialog(true)
	    				var fgas= document.getElementById('fgas');
	    				fgas.value = new Date().toDateInputValue();
	    				fgas.focus();
	    			}
	    			function cancela(){
	    				app.toggleAddDialog(false)
	    				resetea();
	    			}


   					function pdeduc(){
   	   					//obtener el porcentaje de deduccion
   	   					var resul;
   							var deducheck= document.getElementsByName('factorded');
   							var dlongi = deducheck.length;
   							for(var i=0; i<dlongi; i++){
									if(deducheck[i].checked = true){
											resul= deducheck[i].value;
											return resul;
										}
   	   							}
   	   					}

   					function propefec(){
   	   					//obtiene si el check efectivo se pulso
							var resul= document.getElementById("efec").checked;
							return resul;
   	   					}

   						
	    			function borrafilas(){
	    	   				//borra las filas adicionales del dialogo
	    				var filas = document.getElementById('tablamovtos').rows.length;
	    				if (filas != 3){
	        	   				for(var i=0;i<filas-1;i++){
	            	   				switch(i){
	            	   				case 0:
	            	   				case filas-1:
	                	   				break;
	                	   			default:
	                    	   			var filasa=document.getElementById('tablamovtos').rows.length
	                    	   			if(i<=filasa){document.getElementById('tablamovtos').deleteRow(i);}    	   			
	            	   				}
	        	   				}
	     	   				//agrega 1 fila inicial
	        					otracta();
	    	   				}

	    	   			}
	    			function borraforma(dial,forma){
	        				dial.toggleAddDialog(false)
	        				borrafilas();
	        				document.getElementById(forma).reset();
	        				if(bandera==4){location.reload(true);
	        				bandera=0;}
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

	    			function muestrao(){
	    	   			document.getElementById("mensajeo").value="Seleccione las cuentas a afectar.";
	    				app2.toggleAddDialog(true)
	    				document.getElementById('fechao').focus();
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
	    				var metpago = document.getElementById('smpago').value;
	    				var cuenta = document.getElementById('cuenta').value;
	    				var folio = document.getElementById('folio').value;
	    				//obtener porcentaje de deduccion
	    				var pordeduc= pdeduc();
	    				var mprop= document.getElementById('mprop').value;
	    				var efec=propefec();
	    				var ivaaux= document.getElementById('miva').value;
	    				var tipo= "g";
	    				var uuid= document.getElementById('uuid').innerHTML;
	    				//revisar que no este la factura en bd del mes
	    				
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
	 								metpago:metpago,
	 								cuenta:cuenta,
	 								folio:folio,
	 								pordeduc:pordeduc,
	 								mprop:mprop,
	 								efec:efec,
	 								ivaaux:ivaaux,
	 								uuid:uuid,
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
	 								metpago:"",
	 								cuenta:"",
	 								folio:"",
	 								pordeduc:"",
	 								mprop:"",
	 								efec:"",
		 							ivaaux:"",
	 								orig:origen,
	 								dest:destino					
	 							 }, null, "json" )
	 							 .done(function(data) {
	 	    							var resul= data.resul;
	 									app.toggleAddDialog2(false);
	 									location.reload(true);
	 	    						})
	 	    						.fail(function(xhr, textStatus, errorThrown ) {		
	 	    							document.write("ERROR EN REGISTRO "+ errorThrown);
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
					function cambiabt(){
						//cambia el diseño del boton
						var bt = document.getElementById("cancelo");
						var bt2 = document.getElementById("regotra");
							if (bt.className.match("button b")) {
								bt.setAttribute("class","button c");
								bt2.setAttribute("class","button b");
								bt.innerHTML="SALIR";
								bt2.disabled= true;
						    }
						    else {
						    	bt.setAttribute("class","button b");
						    	bt2.setAttribute("class","button a");
						    	bt.innerHTML="Cancela";
						    	bt2.disabled= false;
						    	
						    }
						}
	    			
	    			function rego(){

	 				//al accionar el boton de envio otros
	 				var diferencia = document.getElementById("sumdif").value;
	 				var sumaa = document.getElementById("sumabono").value;
	 				var sumac = document.getElementById("sumcargo").value;
	 				var sumat = Number(sumaa)+Number(sumac);
	 				var forma = document.getElementById("formotro");
	 				
	 				if(diferencia!=0){escribemens("LAS SUMAS NO SON IGUALES.REVISE")}else if(sumat==0){
	 					escribemens("NO SE HAN INTRODUCIDO MONTOS.REVISE")}else{
	 		   				//recoleccion de variables
	 		   				var fecha =	document.getElementById('fechao').value;
	 						var datos= document.getElementsByClassName("dato");
	 		   				var ndatos= datos.length;
	 		   				var scta = document.getElementById('scta').value;
	 		   				var ref = document.getElementById('ref').value;
	 		   				var coment =document.getElementById('coment').value;

	 		   				var datos1= [];

	 		   				for(var i=0;i<ndatos;i++){
	 			   					datos1.push(datos[i].value);
	 			   				}
	 		   				
	 		   				$.post( "php/enviapaciol.php",
	 									{	
	 										fecha:fecha,
	 										scta:scta,
	 										ref:ref,
	 										coment:coment,
	 										cuentas:datos1			
	 									 }, null, "json" )
	 									 .done(function(data) {
	 			    							var resul= data.resul;
	 			    							bandera = 4;
	 			    							cambiabt()
	 											escribemens("CUENTAS AFECTADAS OK. OPRIMA SALIR");
	 			    						})
	 			    						.fail(function(xhr, textStatus, errorThrown ) {
	 				    						console.log( textStatus, errorThrown );	
	 			    							escribemens("ERROR EN EL REGISTRO");
	 										});	
	 					    
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
	    		function otracta(){
	    	   		//añade un renglon para cuentas movtos
	    	   		var nfilas1 = document.getElementsByClassName("selec").length;
	    	   		if(checacta()==0||nfilas1==0){
	            	   		var tablac = document.getElementById("tablamovtos");
	            			var nfilas = nfilas1/2;
	            		    var nfila = tablac.insertRow(nfilas+1);
	            		 	var filas = document.getElementById('tablamovtos').rows;
	            		    var celda=[];
	    	   		    for(var i= 0; i<5; i++){
	    		    		var fila = filas[nfilas+1];
	 					celda[i] = fila.insertCell(i);			
	 		            switch(i){
	     		            case 0:
	     		            	var ocult1 = document.createElement('input');
	     		            	ocult1.setAttribute("id", "elem"+nfilas+i);
	     		            	celda[i].setAttribute("hidden", true)
	     			        	ocult1.setAttribute("value",nfilas);
	     		            	ocult1.type = "number";
	         		            celda[i].appendChild(ocult1); 
	         		            break;
	     		            case 1:    
	     		            case 3:
	     		            	var x = document.createElement("SELECT");
	     		                x.setAttribute("id", "elem"+nfilas+i);
	     		                x.setAttribute("class","selec dato");
	     		                celda[i].appendChild(x);
	     		                var selact = document.getElementById("elem"+nfilas+i)
	     		                llenactas(obj1,selact)
	     				    break;

	     		            case 2:
	     		            	var input1 = document.createElement('input');
	     			        	input1.setAttribute("id", "elem"+nfilas+i);
	     			        	input1.setAttribute("class","texto abonos dato");
	     			        	input1.setAttribute("value",0);
	         		            input1.type = "number";
	         		            input1.step="0.01"
	             		        input1.addEventListener('change',introtext, false)
	         		            celda[i].appendChild(input1); 
	     		            break;
	     			        default:
	         			        var input2 = document.createElement('input');
	     			        	input2.setAttribute("id", "elem"+nfilas+i);
	     			        	input2.setAttribute("class","texto cargos dato");
	     			        	input2.setAttribute("value",0);
	         		            input2.type = "number";
	         		            input2.step="0.01"
	         		            input2.min="0"
	         		           	input2.addEventListener('change',introtext, false)
	         		            celda[i].appendChild(input2);
	 		            }
	 		            
	    	   		    	}	
	    	   					escribemens("seleccione las cuentas a afectar");	   	   		
	    	   	   		} 		
	    	   		}

	    		function introtext(){
	    	   		//suma valores en cajas montos
	    	   		var abonos=document.getElementsByClassName("abonos");
	    	   		var cargos=document.getElementsByClassName("cargos");
	    	   		var sumabono= 0;
	    	   		var sumcargo=0;
	    	   		for (var i = 0; i < abonos.length; i++) {
	    	   			sumabono = sumabono + parseFloat(abonos[i].value);
	    	   			sumcargo = sumcargo + parseFloat(cargos[i].value)	
	 			}
	 			var sumasc=document.getElementById("sumabono");
	 			sumasc.value = sumabono;
	 			var sumasa=document.getElementById("sumcargo");
	 			sumasa.value = sumcargo;
	 			var diferencia = document.getElementById("sumdif");
	 			diferencia.value = sumabono-sumcargo;
	    	   		}
	 			function escribemens(mensaje){
	 				//pone texto en el area de mensajes
	 					var aream = document.getElementById("mensajeo");
	 					aream.value = mensaje;
	 				}

	 			function selece(){
	 				//acciones al cambiar el valor de un elemento
	 				var nombre=this.id;
	 				var inic=nombre.substr(0,5);
	 				var fin=nombre.substr(-1);
	 				var nuevon= Number(fin)+1;
	 				var nuevoe=inic+nuevon;
	 				var nuevoel=document.getElementById(nuevoe);
	 				nuevoel.focus();
	 				}
	 			
	 			function checacta(){
	 				//revisa que se hayan elegido todas las cuentas
	 				var  revisa= document.getElementsByClassName("selec");
	 				var suma;
	 				var mensaje = "No hay suficientes cuentas seleccionadas. Revise";
	 				var resul=0;
	 				for (var i = 0; i < revisa.length; i++) {
	 					var valor = revisa[i].value;
	 					switch(i){
	 					case 0:
	 					case 1:			
	 		   	   			if(valor == 0){
	 			   	   			escribemens(mensaje);
	 			   	   			resul=-1;
	 			   	   			}
	 						break;
	 					default:
	 						suma= suma + valor
	 						if(suma == 0){
	 			   	   			escribemens(mensaje);
	 			   	   			resul=-1;
	 			   	   			}
	 					}
	 					if (resul != 0){break;}
	 				}
	 				return resul;
	 				}

				function cgasto(){
					//acciones de acuerdo a la clase de gasto
					//si deducible mostrar check factor deduc
					var cgasto= this.value;
					var cfded= document.getElementById("fded")
					if(cgasto<"06"){
						if(cfded.classList.contains("ocult")){cfded.classList.toggle("ocult")};
						}else{
							if(!cfded.classList.contains("ocult")){cfded.classList.toggle("ocult")};	
							}
					}
							
	 //insercion de primera línea en dialogo otros movs
	    		otracta();
	 			//escuchas
	 			//	boton mas
	 			document.getElementById("botonm").addEventListener('click',otracta,false)
	    			//boton gasto
	 			document.getElementById("botonp").addEventListener('click',muestrad,false)
	 			//boton traspaso
	 			document.getElementById("botont").addEventListener('click',muestrat,false)
	 			//boton otros movimientos
	 			document.getElementById("botono").addEventListener('click',muestrao,false)
	 			//cajas select
	 			var selecs = document.getElementsByClassName("selec")
	 				for (var i = 0; i < selecs.length; i++) {
	     			selecs[i].addEventListener('change',selece, false);
	 				}
	 					
	 			//cajas montos cargos y abonos
	 			var textos = document.getElementsByClassName("texto")
	 				for (var i = 0; i < textos.length; i++) {
	     			textos[i].addEventListener('change',introtext, false);
	 				}

	 			//boton registro otros
	 			document.getElementById("regotra").addEventListener('click',rego,false)	
	 			//boton registro gast
	 			document.getElementById("reggasto").addEventListener('click',regg,false)
	 			//boton cancela
	 			document.getElementById("butAddCancel").addEventListener('click',cancela,false)
	 			document.getElementById("cancelo").addEventListener('click',function(){borraforma(app2,'formotro')},false)
	 			//boton registro traspaso
	 			document.getElementById("regtras").addEventListener('click',regt,false)
	 			//boton cancela traspaso
	 			document.getElementById("cancelt").addEventListener('click',cancelat,false)
	 			//calculo de iva
	 			document.getElementById("montog").addEventListener('change',calciva,false)
	 			//calculo de total
	 			document.getElementById("ivag").addEventListener('change',calctotal,false)
	 			//clase de gasto
	 			document.getElementById("catg").addEventListener('change',cgasto,false)
	 			//metodo de pago
	 			document.getElementById("smpago").addEventListener('change',modmpago,false)
	 			//concepto
					document.getElementById("concepg").addEventListener('change',mconcep,false)
				//propina
				document.getElementById("mprop").addEventListener('change',ivaaux,false)
	 			//enfoque inicial
	 			document.getElementById("smpago").addEventListener('change',cuentasi,false)
	    		 });
		    

		    }).fail(function() {
		        alert( "error" );
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

				 <br>
				 <h2>REGISTRO DE GASTOS Y OTROS</h2>

				  <?php
				  		include_once "include/menu1.php";
				  ?>

				  
				  <br>
				  <div class="divicent">
				  	<button class="button c" type="button" id="botonp">Registrar Gasto</button>
				  	<button class="button c" type="button" id="botont">Traspasos</button>
				  	<button class="button c" type="button" id="botono">Otros Movimientos</button>
				  </div>
				  <div></div>
				  <br>
				  <div >
				  <?php
				   saldobanco($mysqli);
				  	consultad($mysqli);
				  ?>
				  </div>
				   
		 </main>
		 
		 
		 <!-- caja dialogo registro otros mov -->
		 <div class="dialog-container" id="dialogoo" opacity=100>
    		 <div class="dialog">
    		 	<div class="dialog-title" id="tituloo">OTROS MOVIMIENTOS</div>
    		 	
    		 		<div id="menso" class="divicent">
		    			<label for="mensajeo">MENSAJES: </label>
		    			<textarea name="mensajeo"  id="mensajeo" rows="3" cols="50" disabled></textarea>
		    		</div>
		    		<div class="dialog-body">
		    			<form id="formotro" method ="post" action="#" onsubmit="return false;" >
		    			<div class="divicent"><label for="fechao">FECHA DE APLICACION</label><input type="date" id="fechao"></div>
		    			<table id="tablamovtos" class="tabladia" >
		    				<tr><th hidden=true></th><th>DEBE</th><th>Monto</th><th>HABER</th><th>Monto</th>
		    				<th><button id="botonm">+</button></th></tr>
		    				<tr><td>SUMAS IGUALES</td>
		    				<td><input type ="number" disabled id="sumabono" name = "sumabono" value=0></td>
		    				<td></td>
		    				<td><input type ="number" disabled id="sumcargo" name = "sumcargo" value=0></td>
		    				<td><input type ="number" disabled id="sumdif" name = "sumdif" value =0></td></tr>
		    			</table>
		    		<div class ="rengn">
		    		<label for="ref">SUBCUENTA</label><input type="text" id="scta" name ="scta" class="cajac">
		    		<label for="ref">REFERENCIA</label><input type="text" id="ref" name ="ref" class="cajac">
		    		<label for="coment">COMENTARIOS</label><input type="text" id="coment" name ="coment" class="cajam">
		    		</div>
		    			<div class="dialog-buttons">
		    				<button type="submit" id="regotra"  name="regotra" class="button a">Registrar</button>
						     <button type="submit" id="cancelo" class="button b" >Cancelar</button>
		    			</div>
		    			</form>
		    		</div>
    		 	
    		 </div>
		 
		 </div>
		 <!-- caja dialogo registro gasto -->
		 
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
			    			<label>TOTAL:</label><input type="text" name="totalg" id="totalg"/>
			    			</div>
			    			<div	 class="rengn">
			    			<label>Concepto Orig: </label><input type="text" name="concepo"  id="concepo" class="cajalfc"/>
			    			</div>
			    			<div class="rengn">
			    				<label>Categoría: </label>
			    				<select id="catg" name="catg">
									<option value="0">Seleccione la clase de gasto</option>
									<option value="601">Gastos Generales</option>
									<option value="602">Gastos de Venta</option>
									<option value="603">Gastos de Administración</option>
									<option value="701.10">Comisiones Bancarias</option>
									<option value="601.83">Generales No Deduc</option>
									<option value="602.83">Ventas No Deduc</option>
									<option value="603.81">Admon No Deduc</option>
									<option value="703">Otros Gastos Deducibles</option>
		         				</select>
		         				<label>Concepto: </label><input type="text" name="concepg"  id="concepg" class="cajam" maxlength="20"/>		
			    				<div class="rengn">
    		         				<span id="fded" class= "ocult" >
    		         					<label>%deduc: </label>
    		         					<input type="radio" name="factorded" value=1 checked > Al 100%
        			    				<input type="radio" name="factorded"value =.08> Al 8.75%
    		         				</span>
		         				</div>
			    			</div>
			    			<label>Metodo de Pago: </label>
			    			<div class="rengn">
			    				<select id="smpago" name="smpago">
									<option value="0">Seleccione el medio de pago</option>
									<option value="01">Efectivo</option>
									<option value="02">Cheque</option>
									<option value="03">Transferencia</option>
									<option value="13">Cargo a cuenta</option>
									<option value="04">Tarjetas de Credito</option>
									<option value="28">Tarjetas de Débito</option>
									<option value="99">Otros</option>
		         				</select>
		         				<label>Cuenta: </label><input type="text" name="cuenta"  id="cuenta" class="cajac" maxlength="4" />
		         				<label>Folio Op: </label><input type="text" name="folio"  id="folio" class="cajac" />
			    			</div>
			    			<div>
			    				<label id="ladic" class="adic"> </label><input type="number" name="mprop"  id="mprop" class="cajac adic" />
			    				<label id="lefec" class="adic" >Efectivo? </label><input type="checkbox" name="efec"  id="efec" class="cajac adic" />
			    				<label id="liva" class="adic">IVA </label>
			    				<input type="number" name="miva" id="miva" class="cajac adic" step="0.1"/>
			    			</div>
			    			<div class="rengn">
			    				<h4 id="avisor"></h4>
			    			</div>
			    			<div class="dialog-buttons">
			    				<button type="submit" id="reggasto" class="button a">Registrar</button>
						      	<button type="submit" id="butAddCancel" class="button b" >Cancelar</button>
						    </div>
						    <span id="uuid" class="ocult"></span>
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
									<option value="102.01">Banco</option>
		         				</select>
		         				<label>A la Cuenta: </label>
			    				<select id="destinot" name="destinot">
									<option value="0">Seleccione la cuenta destino</option>
									<option value="101.01">Caja</option>
									<option value="102.01">Banco</option>
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
