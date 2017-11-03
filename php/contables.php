<!DOCTYPE html>
<html>
	<head>
	  <meta charset="utf-8">
	  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <title>Vannetti Cucina contables </title>
	  <link rel="shortcut icon" href="../img/logomin.gif" />  
	  <link rel="apple-touch-icon" href="../img/logomin.gif">
	  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	  <script>
	  	'use strict';
	  	(function() { 
	  		$(document).ready(function() {
	  			document.getElementById('transiva').addEventListener('click', function() {
				alert('en construccion');
				},false) 
	  			
	  		});
	  		
	  	})();
	  </script>
	</head>
	
	<body>
	<header>
		<button id='transiva'>Transferir iva</button>
	</header>
	
	<main>
		
	

<?php
    /*** Esta rutina obtiene los valores de los indicadores***/
    /*** del proceso contable***/
	/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
	
	function mesmax($mysqli){
		/*** obtiene el valor del mes maximo registrado ***/
		$sql = "SELECT MAX(fecha) FROM diario;";
			// Execute the query here now
			$query=mysqli_query($mysqli, $sql) or die ("ERROR EN CONSULTA DE FECHA MAXIMA ".mysqli_error($mysqli));
			$row = mysqli_fetch_row($query);
			$fechamax= new DateTime($row[0]);
			$fechamax->modify('+1 day');
			return $fechamax;
	}
	
	
	function ultfin($mysqli){
		/*** obtiene el valor de la fecha maxima de cierre ***/
		$sql = "SELECT MAX(fechafin) FROM saldos;";
			// Execute the query here now
			$query=mysqli_query($mysqli, $sql) or die ("ERROR EN CONSULTA DE FECHA saldo MAXIMA ".mysqli_error($mysqli));
			$row = mysqli_fetch_row($query);
			$fechamax= new DateTime($row[0]);
			return $fechamax;
	}
	
	
	function balanza($mysqli,$ultcierre){
		
		$ultcierremod = $ultcierre->format('Y-m-d');
		$crea="CREATE TEMPORARY TABLE balanza
		    (cuenta DECIMAL (6,2),
		    nombre VARCHAR(60),
		    Natur VARCHAR(1),
		    saldoi DECIMAL(15,6),
		    debe  DECIMAL(15,6),
		    haber DECIMAL(15,6),
		    saldof DECIMAL(15,6)
		    );";
		$query1=mysqli_query($mysqli, $crea) or die ("ERROR EN CREACION TABLA ".mysqli_error($mysqli));
		
		$sinicial= "INSERT INTO balanza
		    SELECT t1.cuenta, t2.Desc, t2.Natur,saldo,0,0,0 FROM saldos AS t1 INNER JOIN catctasat AS t2 ON t1.cuenta = t2.NumCta 
		    WHERE fechafin = '".$ultcierremod."' AND facturar = 0;";
	    	$query2=mysqli_query($mysqli, $sinicial) or die ("ERROR EN CONSULTA SALDOS INICIALES ".mysqli_error($mysqli));
	    	
	    $movsmes=" UPDATE balanza  AS t1 
		SET debe = COALESCE((SELECT SUM(t2.debe) FROM diario AS t2 WHERE t1.cuenta = t2.cuenta  AND fecha > '$ultcierremod' ),0),
		haber = COALESCE((SELECT SUM(t2.haber) FROM diario AS t2 WHERE t1.cuenta = t2.cuenta AND fecha  >'$ultcierremod'),0);";
		
		$querymovs= mysqli_query($mysqli, $movsmes) or die ("ERROR EN CONSULTA MOVIMIENTOS".mysqli_error($mysqli));
		
		$saldosd="UPDATE balanza SET saldof = saldoi + debe - haber WHERE Natur = 'D';";
		
		$querysaldosd = mysqli_query($mysqli, $saldosd) or die ("ERROR EN CALCULO SALDOS d".mysqli_error($mysqli));
		
	    $saldosa="UPDATE balanza SET saldof = saldoi - debe  + haber WHERE Natur = 'A';";
		
		$querysaldosa = mysqli_query($mysqli, $saldosa) or die ("ERROR EN CALCULO SALDOS a".mysqli_error($mysqli));
		
		$totales = "INSERT INTO balanza (nombre,debe,haber) SELECT 'SUMAS',SUM(debe),SUM(haber) FROM diario WHERE fecha  > '$ultcierremod' ;";
		
		$querytot = mysqli_query($mysqli, $totales) or die ("ERROR EN CALCULO TOTALES".mysqli_error($mysqli));
			
    	$selecciona ="SELECT * FROM balanza ORDER BY cuenta";
		
		$query3= mysqli_query($mysqli, $selecciona) or die ("ERROR EN CONSULTA SELECCION 1 ".mysqli_error($mysqli));
		
		while ($fila = mysqli_fetch_array($query3)) {
				echo"<tr>";
					for ($i=0; $i < 7; $i++) {
						If($i>2){
							echo "<td>".number_format($fila[$i],2)."</td>";	
						}else{
							echo "<td>".$fila[$i]."</td>";	
						}			
								 	
					}
				echo"</tr>";
		}
			
	    	
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
	/**trae fechas de la consulta**/
    if (is_object($mysqli)) {
    	$fechai= mesmax($mysqli);
		$ultcierre = ultfin($mysqli);
		$fechaf = $fechai->modify('last day of');
		$fechai2= ultfin($mysqli);
		$fechai2->modify('+1 day');
		$result = $fechai2->format('Y-m-d');
		$result2= $fechaf->format('Y-m-d');
		echo "<h3> PHP List All Session Variables</h3>";
   		foreach ($_SESSION as $key=>$val)
    		echo $key." ".$val."<br/>";
		echo "<div>
				<table border='1' cellspacing='5' cellpadding='5' style='width:80%'>
					<tr><th colspan='7'>";
					echo "BALANZA DE COMPROBACION DEL ".$_SESSION['fechainic']." AL ".$result2."</th></tr>";
					echo "<tr><th>CUENTA</th><th>DESCRIPCION</th><th>NAT</th><th>INICIAL</th><th>DEBE</th><th>HABER</th><th>SALDO</th></tr>";
		          	$tabla=balanza($mysqli,$ultcierre);
		    echo"</table>
    		</div>";
		

    }else{die ("<h1>'No se establecio la conexion a bd'</h1>");};
    		  
	/* cerrar la conexion */

	 mysqli_close($mysqli);
	 
	 ?>
		</main>
		
		<footer>
			<h3>Vannetti Cucina</h3>
		</footer>
	</body>
</html>