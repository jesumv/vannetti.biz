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
	   $result1=$mysqli->query("SELECT (SUM(CASE WHEN cuenta='401.01' THEN haber ELSE 0 END)+SUM(CASE WHEN cuenta='401.04' THEN haber ELSE 0 END))FROM DIARIO");
	   $dato1=$result1->fetch_row();
	   $result2=$mysqli->query("SELECT SUM(CASE WHEN cuenta='501.01' THEN debe ELSE 0 END)FROM DIARIO");
	   $dato2=$result2->fetch_row();
	   $result3=$mysqli->query("SELECT SUM(CASE WHEN cuenta='201.01' THEN haber ELSE 0 END)FROM DIARIO");
	   $dato3=$result3->fetch_row();
	   $result4=$mysqli->query("SELECT SUM(CASE WHEN cuenta='201.01' THEN debe ELSE 0 END)FROM DIARIO");
	   $dato4=$result4->fetch_row();   
	   $result5=$mysqli->query("SELECT SUM(CASE WHEN cuenta='105.01' THEN debe ELSE 0 END)FROM DIARIO");
	   $dato5=$result5->fetch_row();
	   $result6=$mysqli->query("SELECT SUM(CASE WHEN cuenta='105.01' THEN haber ELSE 0 END)FROM DIARIO");
	   $dato6=$result6->fetch_row();
	   $result7=$mysqli->query("SELECT SUM(CASE WHEN cuenta='101.01' THEN debe ELSE 0 END)FROM DIARIO");
	   $dato7=$result7->fetch_row();
	   $result8=$mysqli->query("SELECT SUM(CASE WHEN cuenta='101.01' THEN haber ELSE 0 END)FROM DIARIO");
	   $dato8=$result8->fetch_row();
	   $result9=$mysqli->query("SELECT SUM(CASE WHEN cuenta='102.01' THEN debe ELSE 0 END)FROM DIARIO");
	   $dato9=$result9->fetch_row();
	   $result10=$mysqli->query("SELECT SUM(CASE WHEN cuenta='102.01' THEN haber ELSE 0 END)FROM DIARIO");
	   $dato10=$result10->fetch_row();
	   $vta=number_format($dato1[0],2);
	   $cvta=number_format($dato2[0],2);
	   $ubruta=$vta-$cvta;
	   $prov=number_format($dato3[0]-$dato4[0],2);
	   $cxc=number_format($dato5[0]-$dato6[0],2);
	   $caja=number_format($dato7[0]-$dato8[0],2);
	   $bancos=number_format($dato9[0]-$dato10[0],2);
	   $mysqli->close(); 
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
  <meta name="description" content="">
  <meta name="author" content="jmv">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="img/logomin.gif" />  
	<link rel="apple-touch-icon" href="img/logomin.gif">
	<link rel="stylesheet" href= "css/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href= "css/movil.css" />
	<script src="js/jquery.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	
	<script>
	'use strict';
	(function() {

	})();	
	</script>

</head>

<body>
	<div data-role="page" id="portalmov">
		<div data-role="header">
    		<a href="portalmov.php" data-ajax="false" class="ui-btn-left ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-home">Inicio</a>
			<h1>Vannetti Cucina App</h1>
    		<a href="logout.php" data-ajax="false" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Salir</a>
		</div>
		<div class="ui-content">
			<a href="#navpanel" class="ui-btn ui-shadow ui-corner-all ui-btn-inline ui-btn-icon-left ui-icon-bars">Navegaci&oacute;n</a>
			<h2>INDICADORES DE DESEMPEÑO</H2>
				<h2>MES DE AGOSTO 2016</h2>
  <table id="kpo">
  	<tr>
	  	<th>INDICADOR</th>
	  	<th>VALOR</th>
	</tr>
  	<tr>
  		<td>VENTAS</td>
  		<?php 
  		echo'<td>'.$vta.'</td><td></td><td>PROVEEDORES</td><td>'.$prov.'</td></tr>';
  		echo'<tr><td>MENOS</td></tr><tr><td>COSTO DE VENTAS</td><td>'.$cvta.'</td><td></td><td>CUENTAS POR COBRAR</td><td>'.$cxc.'</td></tr>';
  		echo '<tr><td>UTILIDAD BRUTA</td><td>'.$ubruta.'</td><td></td><td>CAJA</td><td>'.$caja.'</td></tr>';
		echo '<tr><td></td><td></td><td></td><td>BANCOS</td><td>'.$bancos.'</td></tr>';
  		?>
  		
  </table>
	 
		</div>
		<div data-role="panel" id="navpanel" data-display="overlay">
	 		<ul data-role ="listview">
	 			<li><a href="oc.php" data-ajax="false">Ordenes de Compra</a></li>
	 			<li><a href="listoc.php" data-ajax="false">Rec. de OC</a></li>
		    	<li><a href="pedido.php" data-ajax="false">Pedidos</a></li>
		    	<li><a href="listasp.php" data-ajax="false">Listas de Productos</a></li>
		    	<li><a href="portal.php" data-ajax="false">Portal</a></li>
	 		</ul>	    	
 		</div>
	</div>
  
</body>
</html>
