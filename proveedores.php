<?php
function myAutoload($ClassName)
{
    require('include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

    //directiva a la conexion con base de datos
    $funcbase = new dbutils;
    $mysqli = $funcbase->conecta();
	
 /*** si se establecio la conexion***/
    if (is_object($mysqli)) {
    	/*** checa login***/
    	$funcbase->checalogin($mysqli);     
    } else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
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
  <link rel="stylesheet" type="text/css" href="css/inline.css">
  <link rel="stylesheet" type="text/CSS" href="css/dropdown_two.css" />
  <link rel="stylesheet" type="text/css" href="css/plant1.css">
  <link rel="shortcut icon" href="img/logomin.gif" />  
  <link rel="apple-touch-icon" href="img/logomin.gif">
  <script src="js/jquery3/jquery-3.0.0.min.js"></script>
  <script src="js/altaprov.js"></script>
   <script>
   'use strict';
	(function() {
		$(document).ready(function() {
			//traer funciones auxiliares
			 $.getScript( "js/app.js");
			  document.getElementById('butenvia').addEventListener('click', function() {
				    // muestra el dialogo  de alta
				    alta();
				  }); 
			});
			
 //rutinas de edicion y eliminacion de productos
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
  	<br />
 <h2>PROVEEDORES</h2>
  	<?php
/*menu de navegación*/
include_once "include/menu1.php";   	
  	?>
  	<div>
  	 	<button id="primero" class="button c">ALTA PROVEEDOR</button> 
  	</div>

   <?php
//-----CONSTRUCCION DE LA TABLA------------------------------------------------------------------------
 $table = 'proveedores';
 $sql= "SELECT idproveedores,razon_social,nom_corto,direccion,contacto,diascred FROM $table WHERE status < 2 ";
 $result2 = mysqli_query($mysqli,$sql) or die();

    if(mysqli_num_rows($result2)) {
        echo '<table cellpadding="0" cellspacing="0" class="db-table">';
        echo '<tr><th>Editar</th><th>Eliminar</th><th>No.</th><th>Nombre</th><th>Nombre Corto</th><th>Dirección</th><th>Contacto</th><th>D Cred</th></tr>';
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
    }else{echo '<h2>No hay proveedores a mostrar</h2>';}
 
 
  /* liberar la serie de resultados */
  mysqli_free_result($result2);
  /* cerrar la conexi�n */
  mysqli_close($mysqli);
  
  ?> 
  </main>
  
  <div class="dialog-container">
      <div class="dialog">
      <div class="dialog-title">Datos del Proveedor</div>
      <div class="dialog-body">
        <!-- la caja para registro de proveedor -->
        <form id="altaprov" name="altaprov" method ="post" action="#" onsubmit="return false;" >
        	<div >
			  <label>Razón Social:</label><input type="text" name="razon" id="razon" class="cajal" />
			</div>
            <div>
            	<label>RFC:</label><input type="text" name="rfc" id="rfc" class="cajam"/>
            	<label>Nombre Corto:</label><input type="text" name="nomcor" id="nomcor" class="cajam"/>
            </div>
            <div>
            	<label>Direccion</label><input type="text" name="dir"  id="dir" class="cajal"/>
            </div>
           <div>
               	<label>Teléfono:</label><input type="tel" name="telef"  id="telef" class="cajam" />
                <label>Contacto:</label><input type="text" name="cont"  id="cont" class="cajam" />
           </div>
            <div>
             	<label>Correo:</label><input type="email" name="correo"  id="correo" class="cajal" />
            </div>
            <div>
                <label>Factura:</label><input type="checkbox" name="factura"  id="factura" class="cajam" value="true"/>
                <input type="hidden" name="factura" value="false"/>
                <label>Días Credito:</label><input type="number" name="dcred"  id="dcred" class="cajam" />
            </div>
             
       
      </div>
      <div class="dialog-buttons">
      		<button id="butenvia" class="button a">Enviar</button> 
           <button id="butAddCancel" class="button b">Cancelar</button>        
      </div>  
      </form>

    </div>
  </div> 
</body>
<footer></footer>

</html>
