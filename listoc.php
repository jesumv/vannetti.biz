<?php
function myAutoload($ClassName)
{
    require('include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

//directiva a la conexion con base de datos
$funcbase = new dbutils;
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="img/logomin.gif" />  
	<link rel="apple-touch-icon" href="img/logomin.gif">
	<link rel="stylesheet" href= "css/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href= "css/movil.css" />
	<script src="js/jquery-2.2.4.min.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	<script src="js/jquery.number.js"></script>
</head>

<body>
  <div data-role="page" id="paglistoc"> 
    <div data-role="header">
    <a href="portalmov.php" data-ajax="false" class="ui-btn-left ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-home">Inicio</a>
		<h1>O. Compra x Recibir</h1>
    <a href="logout.php" data-ajax="false" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
	</div>
	<div class="ui-content">
		<a href="#navpanel" class="ui-btn ui-shadow ui-corner-all ui-btn-inline ui-btn-icon-left ui-icon-bars">Navegaci&oacute;n</a>
		<form id="listoc">
			<label for="locprov" class="select">Proveedor</label>
			<select id="locprov" name="locprov">
				<option value="0">Seleccione al proveedor</option>
			</select>	
		</form>
		<div data-role="controlgroup" id="lista"></div>
		<div data-role="popup" id="aviso">
			<p>Sin texto, todavía.</p>
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
	  <div class="loader">
		    <svg viewBox="0 0 32 32" width="32" height="32">
		      <circle id="spinner" cx="16" cy="16" r="14" fill="none"></circle>
		   </svg>
	</div>
</body>
<script src="js/fauxlistoc.js"></script>
</html>
