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
  <title>Vanneti Cucina</title>
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
	 <h2>CONSULTA DE INVENTARIOS</h2>
	 <h3>Ultimo movimiento:</h3>
	  
	  <?php
	  		include_once "include/menu1.php";
	  ?>

<table id"tblinv"name= "tblinv" class="db-table">
	<tr><th>PRODUCTO</th><th>CODIGO</th><th>EXISTENCIA</th><th>MINIMO</th><th>FALTAN</th></tr>
	

<?php
//-----CONSTRUCCION DE LA TABLA------------------------------------------------------------------------
 $table = 'productos';
 $table2 = 'inventario';
 $sql= "SELECT t2.idproductos,t2.codigo, t2.nombre, (SUM(CASE WHEN t1.tipomov=1 THEN t1.cant ELSE 0 END)-SUM(CASE WHEN t1.tipomov=2 THEN t1.cant ELSE 0 END))
 AS total FROM inventario AS t1 RIGHT JOIN productos AS t2 ON t1.idproductos = t2.idproductos WHERE t2.status <2 GROUP BY t2.idproductos ORDER BY t2.nombre ";
 
 $result2 = mysqli_query($mysqli,$sql)or die ("ERROR EN CONSULTA DE INVENTARIOS.".mysqli_error($mysqli));;
  if(mysqli_num_rows($result2)) {
  	 while($row2=mysqli_fetch_row($result2)){
  	 				if($row2[3]==""){$exis=0;}else{$exis=$row2[3];};
				 	echo "<tr><td>$row2[1]</td><td>$row2[2]</td><td>$exis</td></tr>";
				 } 
  }
?>

</table>

  <div class="dialog-container">
    <!-- Insert add-new-city-dialog.html here -->
  </div>

  
  
</body>
</html>

