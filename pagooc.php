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
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pago de OC</title>
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="Weather PWA">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Insert link to styles here -->
   <!-- Insert link to styles here -->
  <link rel="stylesheet" type="text/css" href="css/inline.css">
  <link rel="stylesheet" type="text/CSS" href="css/dropdown_two.css" />
  <link rel="stylesheet" type="text/css" href="css/plant1.css">
  <link rel="shortcut icon" href="img/logomin.gif" />  
  <link rel="apple-touch-icon" href="img/logomin.gif">
  <link rel="stylesheet" href="js/jquery-ui.min.css">
  <script src="js/jquery.js"></script>
  <script src="js/jquery-ui.min.js"></script>
  <script>
  'use-strict';  	
  (function(){
   //rutinas del modelo ap
  	$(document).ready(function() {
  		 var app = {
		    isLoading: true,
		    spinner: document.querySelector('.loader'),
		    container: document.querySelector('.main'),
		    addDialog: document.querySelector('.dialog-container'),
		  };
  	//solo de ejecuta si existen oc
  		if ($(".fila")[0]){			
		   app.toggleAddDialog = function(visible) {
    			if (visible) {
      				app.addDialog.classList.add('dialog-container--visible');
    			} else {
      				app.addDialog.classList.remove('dialog-container--visible');
    			}
    	 
	  	
    	//escucha del boton OK
    	  document.getElementById('cierra').addEventListener('click', function() {
	    // oculta el dialogo de datos de producto
	    	app.toggleAddDialog(false);
	  	}, false); 
	  	
	  	
  	};
		  var editables = document.getElementsByClassName('celda');
		  var ii=0;
		  do{
		  	var listener = function(e){enviar(e.target.id)};
		  	editables[ii].addEventListener('click',listener,false);
		   ii++;
		  }while(ii < editables.length)			    

  			    
  			var loc = document.getElementsByClassName('banco')
	    	var longi=loc.length;
		  	var lbancos=["BANCOMER","CAJA"];
		  	var oc = document.getElementsByClassName('oc')
		  	var z =0;
				    do{ 
				    	var coc=oc[z].innerHTML;
				    	$( "#banco".concat(coc)).autocomplete({
				      		source: lbancos
				    	});
				    	z++;
				    }while(z<longi) 
  			    	
	    	$("#coment1").focus();
	    	
	    	
	  			function aviso(texto){
			  		var recep=document.getElementById('recep').innerHTML=texto;
					app.toggleAddDialog(true);
	  			}
	  			
	  			function enviar(cid){
  				//esta funcion envia los datos del pago
  				//extraer el numeral
  					var oc = cid.substr(3,1);
  				//recoger los datos
  					var prov= document.getElementById('prov'.concat(oc));
  					var monto= document.getElementById('monto'.concat(oc));
  					var iva= document.getElementById('iva'.concat(oc));
  					var total= document.getElementById('total'.concat(oc));
	  				var coment= document.getElementById('coment'.concat(oc));
					var banco= document.getElementById('banco'.concat(oc));
					var fac= document.getElementById('fac'.concat(oc));
	  				var fol= document.getElementById('fol'.concat(oc));
	  				 //solo se ejecuta si existen los datos
	  				if(coment&&banco&&fac&&fol){
	  					var provv= prov.value;
	  					var montov = monto.innerHTML;
	  					var ivav = iva.innerHTML;
	  					var totalv = total.innerHTML;
		  				var comentv = coment.value;
	  					var bancov = banco.value;
	  					var facv = fac.value;
	  					var folv = fol.value;
	  					//enviar la consulta
						$.post( "php/enviapago.php",
							{	oc:oc,
								prov:provv,
								monto:montov,
								iva:ivav,
								total:totalv,
								coment:comentv,
								banco:bancov,
								fac:facv,
								fol:folv,	
							 }, null, "json" )
	    						.done(function() {
	    						    var rcur=document.getElementById('r'.concat(oc))
	    							rcur.style.backgroundColor ="Chartreuse";
	    							var celcur=document.getElementById('pag'.concat(oc))
	    							celcur.style.backgroundColor ="#008040";
	    							document.getElementById('coment'.concat(oc)).disabled=true;
	    							document.getElementById('banco'.concat(oc)).disabled=true;
	    							document.getElementById('fac'.concat(oc)).disabled=true;
	    							document.getElementById('fol'.concat(oc)).disabled=true;
	    							document.getElementById('pag'.concat(oc)).removeEventListener('click', listener, false);
	    							aviso("REGISTRO DE PAGO OK")
	    							app.isLoading = true;
	    							setTimeout(function(){ location.reload();  }, 3000);
	    						})
	    						.fail(function( data ) {
	    							var err1 = data.success;
	    							aviso("error en registro de pago: "+err1);
								})
  					
	  				}else{alert("intente de nuevo")} 

  				}
		}
if (app.isLoading) {
	      		app.spinner.setAttribute('hidden', true);
	      		app.container.removeAttribute('hidden');
	      		app.isLoading = false;
	    	}
	 });
	  	  	
   })();
    
  </script>  

</head>
<body>

  <header class="header">
    <h1 class="header__title">Bienvenido(a), <?php echo $_SESSION['nombre']; ?></h1>
  </header>

  <main class="main">
<br />
<br />
<br />

<div></div>
  	<?php
/*menu de navegación*/
include_once "include/menu1.php";   	
echo'<div class="titulo"> <h2>PAGO DE ORDENES DE COMPRA</h2></div>';

//-----CONSTRUCCION DE LA TABLA------------------------------------------------------------------------
 $table = 'oc';
 $table2 = 'proveedores';
 $sql= "SELECT t1.idoc,t1.idproveedores,t2.razon_social,t1.monto,t1.iva,t1.total,t1.coment  FROM $table AS t1 INNER JOIN $table2 AS t2 
 ON t1.idproveedores=t2.idproveedores WHERE t1.status = 2 OR t1.status = 3 ORDER BY t1.idoc";
 
 $result2 = mysqli_query($mysqli,$sql);

    if(mysqli_num_rows($result2)) {
        echo '<table cellpadding="0" cellspacing="0" class="db-table">';
        echo '<tr><th>No. OC</th><th>PROVEEDOR</th><th>MONTO</th><th>IVA</th><th>TOTAL</th><th>COMENT</th><th>BANCO</th><th>FACT</th><th>FOLIO</th><th>PAGADA</th></tr>';
        //inicializacion de contador de renglon
        $reng = 0;
        while($row2 = mysqli_fetch_assoc($result2)){
            $id = $row2['idoc'];
			$idprov=$row2['idproveedores'];
            echo '<tr id=r'.$id.' class="fila">';
            foreach($row2 as $key=>$value) {
            	switch ($key) {
					case 'idproveedores':
						echo'<input type="hidden" id=prov'.$id.' name=prov'.$id.' value="'.$idprov.'"/>';
						break;
					case 'coment':
						echo '<td><input type="text" name=coment'.$id.' id=coment'.$id.' class="cajalfc" value="'.$value.'"/></td>';
						break;
					case 'idoc':
						echo '<td class="oc">'.$value.'</td>';
						break;
					case 'monto':
						echo '<td id=monto'.$id.'>'.$value.'</td>';
						break;
					case 'iva':
						echo '<td id=iva'.$id.'>'.$value.'</td>';
						break;
					case 'total':
						echo '<td id=total'.$id.'>'.$value.'</td>';
						break;
					default:
						echo '<td>'.$value.'</td>';
						break;
				}
            }
			echo '<td><div class="ui-widget"><input class=banco id=banco'.$id.'></div></td>';
			echo '<td><input type="text" id=fac'.$id.' class="cajam"/></td>';
			echo '<td><input type="text" id=fol'.$id.' class="cajam"/></td>';
			echo '<td class = "celda" id= pag'.$id.'><a href ="#" ><img src="img/check-white.png" 
			ALT="pagar"></a></td>';
            echo '</tr>';
			$reng++;
        }
        echo '</table><br />';
    }else{echo '<div class="subt"><h2>No hay ordenes de compra por pagar</h2></div>';}
 
 
  /* liberar la serie de resultados */
  mysqli_free_result($result2);
  /* cerrar la conexi�n */
  mysqli_close($mysqli);
  
  ?> 
  </main>
  
  <div class="dialog-container">
    <!-- dialogo para pago de orden de compra-->
          <div class="dialog">
      		<div class="dialog-title">Datos del Pago</div>
		      <div class="dialog-body">
		      	<h1 id="recep"></h1>
    		 </div>
    		<button id="cierra" class="button">OK</button>
    	</div>
 </div>
 <div class="loader">
    <svg viewBox="0 0 32 32" width="32" height="32">
      <circle id="spinner" cx="16" cy="16" r="14" fill="none"></circle>
    </svg>
  </div>
</body>
<div id="footer"></div>
</html>

