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
$table = 'clientes';
 $sql= "SELECT idclientes,razon_social,rfc,nom_corto,contacto,tel1,email from clientes where idclientes > 4 AND status <2";
 $result2 = mysqli_query($mysqli,$sql)or die ("ERROR EN CONSULTA DE clientes.".mysqli_error($mysqli));; 

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
   <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
   <script>
   'use strict';
   	(function() { 	
   		
	   	
	   	$(document).ready(function() {
	   		var app = {
			    isLoading: true,
			    spinner: document.querySelector('.loader'),
			    container: document.querySelector('.main'),
			    addDialog: document.querySelector('.dialog-container'),
			  };
			  	
	   	function edita(indice){
	   		alert("en construccion");	
	   	}
	   	
	   	function aedita(){
			 	//esta funcion añade escuchas a botones de pago
			 	var beditar = document.getElementsByClassName('bedit');
			  	for (var i = 0; i < beditar.length; i++) {
			  			beditar[i].addEventListener('click', function(){edita(1)}, false)
			  		}
			}
			
			  
		function limpia(){
				document.getElementById('avisor').innerHTML='';
				document.getElementById('rcte').reset();
			}
		function valida(){
			//revisa que la forma este correctamene llenada
			return 99;
		}
		
		function evalua(resul){
			/** se evalua la respuesta de enviadatoscte.php**/
				var resp;
				switch (resul){
					case -1:
						resp = "ERROR EN CONEXION A BD";
						resulmal(resp);
						break;
					case -2:
						resp = "ERROR EN ALTA CLIENTE";
						resulmal(resp);
						break;
					break;
					default:
						//cerrar dialogo
						app.toggleAddDialog(false);	
				}
			}
		
		function enviacte(){
			//manda datos a bd
			
			//recoleccion de variables de dialogo
			
				var razon= document.getElementById("irazon").value;
				var rfc= document.getElementById("irfc").value;
				var corto= document.getElementById("icorto").value;
				var calle= document.getElementById("icalle").value;
				var noext=document.getElementById("inoe").value;
				var noint= document.getElementById("inoi").value;
				var cp = document.getElementById("icp").value;
				var col = document.getElementById("icol").value;
				var mun = document.getElementById("imun").value;
				var edo = document.getElementById("iedo").value;
				var tel1 = document.getElementById("itel1").value;
				var tel2 = document.getElementById("itel2").value;
				var telc = document.getElementById("itelc").value;
				var cont = document.getElementById("icont").value;
				var corr = document.getElementById("icorr").value;
				var nivel = document.getElementById("snivel").value;
				var diasc = document.getElementById("idiasc").value;
			//envio a bd
				$.post("php/enviadatoscte.php",{razon: razon,
					rfc:rfc,
					corto:corto,
					calle:calle,
					noext:noext,
					noint:noint,
					cp:cp,
					col:col,
					mun:mun,
					edo:edo,
					tel1:tel1,
					tel2:tel2,
					telc:telc,
					cont:cont,
					corr:corr,
					nivel:nivel,
					diasc:diasc		
					},null,"json")
					.done(function(data) {
						var resul= data.resul;
						var textor = evalua(resul);
					})
					.fail(function(xhr, textStatus, errorThrown ) {		
						resulmal("error en registro pago: "+xhr.responseText);
					});	
	
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
						    
			//escucha de boton nuevo cliente
			document.getElementById('nvocte').addEventListener('click', function() {
				//presentar dialogo
					app.toggleAddDialog(true);
					document.getElementById('irazon').focus();
					},false)
					
			//escuchas de elementos dialog
				//escucha de boton cancela
				document.getElementById('CancelCte').addEventListener('click', function() {
					// oculta el dialogo de datos de producto
						app.toggleAddDialog(false);
					//limpia los datos
						limpia();
				},false) 
				
				//escucha de boton registrar cliente
				document.getElementById('regcte').addEventListener('click',function(){
					var aqui= document.getElementById('avisor');
					aqui.innerHTML="";
					var resulcte = valida();
					switch(resulcte){
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
						enviacte();
						break;
					}
				})
				aedita();
	   		});
   	
   	
   		 		
	})();
   </script>

</head>
<body>

  <header class="header">
  	<div>
    	<h1 class="header__title">Bienvenido(a), <?php echo $_SESSION['nombre']; ?></h1>
    </div>
    
  </header>

  <main class="main">
		 <br/>
		 <h2>CATALOGO DE CLIENTES</h2>
		  
		  <?php
		  		include_once "include/menu1.php";
		  ?>
	<br />
	<button id="nvocte" name="nvocte" class="button cent">NUEVO CLIENTE</button>
	<table id"tblcte"name= "tblcte" class="db-table2">
		<tr><th>CLIENTE NO.</th><th>RAZON SOCIAL</th><th>RFC</th><th>NOMBRE CORTO</th><th>CONTACTO</th>
			<th>TELEFONO</th><th>CORREO</th><th>EDITAR</th></tr>
		
	
	<?php
	//-----CONSTRUCCION DE LA TABLA------------------------------------------------------------------------
	  if(mysqli_num_rows($result2)) {
	  	 //construir tabla
	  	 while($row2=mysqli_fetch_row($result2)){
	  	 				$nocte=$row2[0];
			 			$razon=$row2[1];
			 			$rfc=$row2[2];
			 			$corto=$row2[3];
						$contac=$row2[4];
						$tel = $row2[5];
						$correo= $row2[6];
					 	echo "<tr><td id=idcte".$nocte.">$nocte</td>
					 	<td>$razon</td><td>$rfc</td><td>$corto</td><td>$contac</td><td>$tel</td><td>$correo</td>
					 	<td class= 'edac' id=cele".$nocte."><a id=".$nocte." class='bedit' href='javascript:void(0);'>
					 	<img src='img/editar.png' ALT='editar datos'></a></td></tr>";
					 } 
	  }else{echo"<h1>no hay pedidos pendientes de cobro</h1>";}
	?>
	</table>
 </main>
 
 <!-- caja dialogo registro datos -->
  <div class="dialog-container">
    <div class="dialog g">
    	<div class="dialog-title" id="tituloc">DATOS DEL CLIENTE</div>
	    	<div class="dialog-body">
	    		<form id="rcte" method ="post" action="#" onsubmit="return false;">
	    			<div class="rengn">
	    				<label>Razón Social: </label><input type="text" name="irazon"  id="irazon" class="cajaml"/>	
	    			</div>
	    			<div class="rengn">
	    				<label>RFC: </label><input type="text" name="irfc"  id="irfc" class="cajac"/>
	    				<label>Nombre Corto: </label><input type="text" name="icorto"  id="icorto" class="cajam"/>
	    			</div>
	    			<div class="rengn">
	    				<label>DOMICILIO</label>	
		    			<label>Calle: </label><input type="text" name="icalle"  id="icalle" class="cajal"/>
	    			</div>
	    			<div class="rengn">
		    			<label>No. Ext: </label><input type="text" name="inoe"  id="inoe" class="cajac"/>
		    			<label>No. Int: </label><input type="text" name="inoi"  id="inoi" class="cajac"/>
		    			<label>CP: </label><input type="text" name="icp"  id="icp" class="cajac" maxlength="5"/>	
	    			</div>
	    			<div class="rengn">
	    				<label>Colonia: </label><input type="text" name="icol"  id="icol" class="cajal"/>
	    			</div>
	    			<div class="rengn">
	    			<label>Municipio: </label><input type="text" name="imun"  id="imun" class="cajam"/>
	    			<label>Estado: </label><input type="text" name="iedo"  id="iedo" class="cajam"/>	
	    			</div>	
	    			<div class="rengn">
	    			<label>TELEFONOS</label>
	    			<label>1: </label><input type="text" name="itel1"  id="itel1" class="cajac" maxlength="10"/>		
	    			<label>2: </label><input type="text" name="itel2"  id="itel2" class="cajac" maxlength="10"/>
	    			<label>Cel: </label><input type="text" name="itelc"  id="itelc" class="cajac" maxlength="10"/>			
	    			</div>
	    			<div class="rengn">
	    				<label>Contacto: </label><input type="text" name="icont"  id="icont" class="cajal"/>
	    			</div>
	    			<div class="rengn">
	    				<label>Correo: </label><input type="text" name="icorr"  id="icorr" class="cajal"/>
	    			</div >  				
	    				<label>Nivel: </label>
		    			<div class="rengn">
		    				<select id="snivel" name="snivel">
								<option value="0">Seleccione el nivel</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">Costo</option>
	         				</select>
         				<label>Dias Credito: </label><input type="text" name="idasc"  id="idiasc" class="cajac" maxlength="2"/>
		    			</div>
		    			<div class="rengn">
		    				<h4 id="avisor"></h4>
		    			</div>
		    			<div class="dialog-buttons">
					      	<input type="submit" value="Registrar" id="regcte" name = "regcte" class="button a"/>
					      	<button id="CancelCte" class="button b" style="margin-left: 10px">Cancelar</button>
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

