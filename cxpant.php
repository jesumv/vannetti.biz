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
    
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vanneti Consulenti</title>
  <!-- Insert link to styles here -->
   <link rel="stylesheet" type="text/css" href="css/inline.css">
   <link rel="stylesheet" type="text/css" href="css/plant1.css">
   <link rel="stylesheet" type="text/CSS" href="css/dropdown_two.css" />
   <link rel="shortcut icon" href="img/logomin.gif" />  
   <link rel="apple-touch-icon" href="img/logomin.gif">
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

<table id"tblinv"name= "tblinv" class="db-table">
	<tr><th>PROVEEDOR</th><th>FECHA</th><th>OC</th><th>FACTURA</th><th>MONTO</th><th>IVA</th><th>TOTAL</th><th>DIAS VENC</th></tr>
	

<?php
//-----CONSTRUCCION DE LA TABLA------------------------------------------------------------------------
 $table = 'oc';
 $table2 = 'proveedores';
 $sql= "SELECT t2.razon_social,t1.fecharec, t1.idoc,t1.factura,t1.monto,t1.iva,t1.total,t2.diascred FROM $table
 AS t1 INNER JOIN $table2 AS t2 ON t1.idproveedores= t2.idproveedores WHERE t1.status >10 AND t1.credito = 1 ORDER BY fechamov";
 $result2 = mysqli_query($mysqli,$sql)or die ("ERROR EN CONSULTA DE INVENTARIOS.".mysqli_error($mysqli));;
  if(mysqli_num_rows($result2)) {
 
  	 //construir tabla
  	 while($row2=mysqli_fetch_row($result2)){
  	 				$fechamov=date_create($row2[1]);
		 			$diascred=$row2[7];
		 			$fechamod=date_format($fechamov,'Y/m/d');
					$calc=diasvenc($row2[1], $diascred);
				 	echo "<tr><td>$row2[0]</td><td>$fechamod</td><td>$row2[2]</td><td>$row2[3]</td><td>$row2[4]</td><td>$row2[5]</td>
				 	<td>$row2[6]</td><td>".$calc."</td></tr>";
				 } 
  }
?>

</table>

  <div class="dialog-container">
    <!-- Insert add-new-city-dialog.html here -->
  </div>

  
  
</body>
</html>

