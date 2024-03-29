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
        //die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
	       Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Vannetti Cucina</title>
	<meta name="author" content="jmv">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="img/logomin.gif" />  
	<link rel="apple-touch-icon" href="img/logomin.gif">
	<link rel="stylesheet" href= "css/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href= "css/movil.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	<script>
	'use strict';
	(function() {
		
	function quita(){
	 //quitar clientes de la seleccion en el dialogo
	 var ctessel = document.getElementById('prcte');
	 var no = ctessel.length;
	 for(var i=1; i<no; i++) {
		 ctessel.remove(1);
	 }
	 
 }
	$( document ).on( "pageinit", "#listpag", function( event ) {
		
		function imprime(nivel){
	 	//esta funcion trae el pdf de lista de precios
	 	location.href = "#";
		location.href = "php/rlistaprod.php?nivel="+nivel;
	 }
	 
	document.getElementById('prcte').addEventListener('change',function(){
		var respuesta = JSON.parse(prcte.value);
		var nivel = respuesta.nivel;
		imprime(nivel);
		
	});
	document.getElementById('selcte').addEventListener('click', function(){
		$.get('php/getctes.php',function(data){
		var obj1 = JSON.parse(data);
		var men = document.getElementById('prcte');
		for( var z=0; z <obj1.length; z++) {
//extraccion de datos del array
				var id = obj1[z].id;
				var nombre = obj1[z].nombre;
				var nivel =	obj1[z].nivel;	
	//adicion de opciones select
				var option = document.createElement("option");
				option.text = nombre;
				option.value = JSON.stringify({'id':id,'nombre': nombre,'nivel': nivel});
				men.add(option);
			};
			location.href = "#";
			location.href = "#client";
		});	
});

//evento para cerrar dialogo			
 document.getElementById('cierrasel').addEventListener('click',function(){
 	quita();
 	location.href = "#";
 });
		
	  });
			
})();
	</script>
</head>

<body>
 <div data-role="page" id="listpag">

 	<div data-role="header">
 		<a href="portalmov.php" data-ajax="false" class="ui-btn-left ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-home">Inicio</a>
		<h1>Listas de Productos</h1>
    	<a href="logout.php" data-ajax="false" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
 	</div>
 	<div role="main" class="ui-content">
 		<a href="#navpanel" class="ui-btn ui-shadow ui-corner-all ui-btn-inline ui-btn-icon-left ui-icon-bars">Navegaci&oacute;n</a>
 		<button class="ui-btn" id="selcte" data-transition="pop">Seleccionar Cliente</button>
		<button class="ui-btn">Seleccionar Categor&iacute;a</button>
 		<button class="ui-btn">Seleccionar Productos</button>	
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
 
 <div data-role="page" data-dialog="true" data-close-btn="none" id = "client">
 	
 	<div data-role="header">
 		<button class="ui-btn ui-icon-delete ui-btn-icon-notext ui-corner-all"  id="cierrasel" >Sin texto</button>
 		<h3>Seleccione:</h3>
 		</div>
 	<div>
 		<div>
				<select name="prcte" id="prcte">
			    	<option value="0">Seleccione al Cliente</option>
				</select>
		</div>
 	</div>
 	
 	
 </div>
</body>
</html>
