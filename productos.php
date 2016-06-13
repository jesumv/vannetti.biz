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
  <title>Vanneti.biz</title>
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
   //rutinas del modelo ap
  $(document).ready(function() {
  		$.getScript( "js/app2.js");
  		$.getScript( "js/altaprod.js");
        });
  </script>  

</head>
<body>

  <header class="header">
    <h1 class="header__title">Bienvenido(a), <?php echo $_SESSION['nombre']; ?></h1>
  </header>

  <main class="main">
 	<br />
 <h2>PRODUCTOS</h2>
  	<?php
/*menu de navegación*/
include_once "include/menu1.php";   	
  	?>
  <button id="altaprod">ALTA PRODUCTO</button>
  <?php
//-----CONSTRUCCION DE LA TABLA------------------------------------------------------------------------
 $table = 'productos';
 $table2 = 'grupos';
 $table3='unidades';
 $sql= "SELECT t2.nombre, t1.idproductos, t1.codigo, t1.nombre,t3.nombre, t1.cant,
 t1.costo, t1.precio1, t1.precio2,t1.precio3 FROM $table AS t1 INNER JOIN $table2 AS t2 
 ON t1.grupo=t2.idgrupos INNER JOIN $table3 AS t3 ON t1.unidad=t3.idunidades 
 WHERE t1.status < 2 ";
 
 $result2 = mysqli_query($mysqli,$sql);

    if(mysqli_num_rows($result2)) {
        echo '<table cellpadding="0" cellspacing="0" class="db-table">';
        echo '<tr><th>Editar</th><th>Eliminar</th><th>No.</th><th>Código</th><th>Grupo</th><th>Producto</th><th>Unidad</th><th>Cantidad</th><th>Costo</th><th>Precio 1</th><th>Precio 2</th><th>Precio3</th></tr>';
        //inicializacion de contador de renglon
        $reng = 1;
        while($row2 = mysqli_fetch_row($result2)) {
            $id = $row2[0];
            $elid = -$row2[0];
            echo '<tr>';
            echo '<td class= ed id='.$id.'><a href ="modifprov.php?nid='.$id.'"><img src="img/edita.jpg" ALT="editar"></a></td>';
            echo '<td class = el id='.$elid.'><a href ="elimprov.php?nid='.$elid.'"><img src="img/elimina.jpg" ALT="eliminar"></a></td>';
            foreach($row2 as $key=>$value) {
                echo '<td>',$value,'</td>';
            }
            echo '</tr>';
        $reng= $reng++;
        }
        echo '</table><br />';
    }else{echo '<h2>No hay productos a mostrar</h2>';}
 
 
  /* liberar la serie de resultados */
  mysqli_free_result($result2);
  /* cerrar la conexi�n */
  mysqli_close($mysqli);
  
  ?> 
  </main>
  
  <div class="dialog-container">
    <!-- dialogo para alta de producto -->
          <div class="dialog">
      		<div class="dialog-title">Datos del Producto</div>
		      <div class="dialog-body">
		        <form id="altaprod" name="altaprod" method ="post" action="#" onsubmit="return false;">
		        	<div>
		        		<label>Proveedor:</label><select id="selectmenu" name="selectmenu">
											<option value="0">Seleccione al proveedor</option>
         								</select>
         				<label>Grupo:</label><select id="selectmenu2" name="selectmenu2">
											<option value="0">Seleccione el grupo de productos</option>
         								</select>	
		        	</div>
		        	<div >
		        						  
					</div>
		            <div>
		            	<label>Nombre:</label><input type="text" name="nombre" id="nombre" class="cajal"/>
		            </div>
		            <div>
		            	<label>N. Corto</label><input type="text" name="nomcor"  id="nomcor" class="cajam"/>
		            	<label>Código</label><input type="text" name="cod"  id="cod" class="cajac"/>
		            </div>
		           <div>
		           	<label>Unidad:</label><select id="selectmenu3" name="selectmenu3">
											<option value="0">Seleccione unidad medida</option>
         								</select>
		           	 <label>Cantidad:</label><input type="text" name="cant"  id="cant" class="cajac" />
		           </div> 
		           <div>
		            	<label>C Barras</label><input type="text" name="barr"  id="barr" class="cajam"/>
		            	<label>Costo</label><input type="text" name="cost"  id="cost" class="cajac"/>
		            </div>
		            <div>
		            	<label>Descripción</label><input type="text" name="desc"  id="desc" class="cajal"/>
		            </div> 
		            <div>
		            	<label>P1</label><input type="text" name="p1"  id="p1" class="cajac"/>
		            	<label>P2</label><input type="text" name="p2"  id="p2" class="cajac"/>
		            	<label>P3</label><input type="text" name="p3"  id="p3" class="cajac"/>
		            	
		            </div>        
			   </div>
				      <div class="dialog-buttons">
				      </div>
		      		<input type="submit" value=" Enviar "/><br />    
		      	</form>
        <button id="butAddCancel" class="button">Cancelar</button>
    </div>
  </div>
</body>
<div id="footer"></div>
</html>

