<?php
global $num;

//Este script administra lo actualizacion un producto.
//conectar
/*** Autoload class files ***/ 
    function __autoload($class){
      require('include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
	/*** checa login***/
        $funcbase->checalogin($mysqli);
        
		// query de seleccion de combo proveedores
            $query="SELECT idproveedores,nom_corto FROM proveedores ORDER BY nom_corto";
            $result1 = mysqli_query ($mysqli,$query) or die("error en consulta de combo");
		
	function oprimio($mysqli,$numid){
		
	//esta funcion hace las consultas de actualizacion
		$table = 'productos';
	    $desc =strtoupper($_POST ['desc']) ;
		$corto =strtoupper($_POST ['corto']) ;
		$unidad=strtoupper($_POST ['unidad']) ;
		$precio1=strtoupper($_POST ['precio1']) ;
	    $precio2 =strtoupper($_POST ['precio2']) ;
		$precio3 =strtoupper($_POST ['precio3']) ;
	    $precio4=strtoupper($_POST ['precio4']) ;
		$precio5=strtoupper($_POST ['precio5']) ;
		$precio6=strtoupper($_POST ['precio6']) ;
		$preciost=strtoupper($_POST ['preciost']) ;
		$codigo=strtoupper($_POST ['codigo']) ;
	    $usu = $_SESSION['login_user'];
        $noprov = $_POST['noprov'];
		
		// validaciones
		 
	    if (!is_numeric($numid)) {
	        $sqlCommand= "INSERT INTO $table (descripcion,nom_corto,unidad,precio1,precio2,precio3,precio4,precio5,precio6,preciost,
	        codigo,usu,status,idproveedores,alg)
	        VALUES ('$desc','$corto','$unidad',$precio1,$precio2,$precio3,$precio4,$precio5,$precio6,$preciost,
	        '$codigo','$usu',0,$noprov,'$alg')"
	        or die('insercion cancelada '.$table);
			
	    }else {
	        $sqlCommand = "UPDATE $table SET descripcion ='$desc', nom_corto = '$corto', unidad='$unidad',precio1='$precio1',
	        precio2='$precio2',precio3= $precio3, precio4=$precio4, precio5=$precio5,precio6=$precio6,preciost=$preciost,
	        codigo='$codigo',usu = '$usu',status = 1, idproveedores = $noprov, alg = '$alg' WHERE idproductos= $numid LIMIT 1"
	         or die('actualizacion cancelada '.$table);
    	}
	    // Execute the query here now
	    $query = mysqli_query($mysqli, $sqlCommand) or die (mysqli_error($mysqli)); 
	    /* cerrar la conexion */
	    mysqli_close($mysqli);
		
	}
/***si se oprimio el boton de accion***/
if(isset($_POST['enviomod'])){
	$numero = strtoupper($_POST ['num']) ;	
    oprimio($mysqli, $numero);
    header('Location: productos.php');
}
	
/***obtiene los datos del producto por el id recibido en la pagina***/
		
        if(isset($_GET['nid'])){{
                $num=$_GET['nid'];
                $sqlsresul= $funcbase->leetodos($mysqli,'productos','idproductos= '."$num");
				$codigo= $sqlsresul[2];
				$cbarras = $sqlsresul[3];
				$nombre = $sqlsresul[4];
				$nom_corto = $sqlsresul[5];
				$grupo = $sqlsresul[6];
				$unidad = $sqlsresul[7];
				$cant = $sqlsresul[8];
				$descr = $sqlsresul[9];
				$costo = $sqlsresul[10];
				$precio1 = $sqlsresul[11];
				$precio2= $sqlsresul[12];
				$precio3=$sqlsresul[13];
				$idprov = $sqlsresul[14];
				$status = $sqlsresul[15];
				if (is_object($mysqli)) {
					$mysqli = $funcbase->conecta();
					$proveedor = $funcbase->leeprov($mysqli,'idproveedores= '."$idprov");
				}else{die ("<h1>'No se establecio la conexion a la tabla proveedores'</h1>");}
                $titbot = "Actualizar";
				$titulo= "ACTUALIZACION PRODUCTOS";
                
            }
    
        }else{
        die("<h1>NO HAY DATOS PARA CONSTRUIR LA PAGINA</h1>");
        }  	
		
	}else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1"/>
<link rel="stylesheet" href="css/cupertino/jquery-ui-1.10.4.custom.css">
<link rel="stylesheet" type="text/CSS" href="css/plantilla2.css" />
<link rel="stylesheet" type="text/CSS" href="css/dropdown_two.css" />
<link rel="stylesheet" href="css/cupertino/jquery-ui-1.10.4.custom.css">
<link rel="shortcut icon" href="img/logomin.gif" />
<script src="js/jquery-1.10.2.js"></script>
<script src="js/jquery-ui-1.10.4.custom.js"></script>
<script src="js/jquery.validate.js"></script>
<script src="js/validaciones.js"></script>
<script src="js/additional-methods.js"></script>

<title>STELLUS MEDEVICES</title>
  <script>
   $( document ).ready(function() {
       $('#inic').focus();
       $('#prov').autocomplete({
			autoFocus: true,
            source: "php/getprovs.php",
            minLength: 1,
            select:function(event, ui){
            	$("#prov").val( ui.item.nombre);
            	$("#noprov").val(ui.item.id);
            	$('#codigo').focus();	
            }
		                
        }); 
                //funcion para validar la captura de la forma. ya hay una en altaprod.js


        });

  </script>
</head>

<body>

<!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->  
    <?php
 
  include_once "include/barrasup.php";
  ?> 
  <div class = "centraelem">
  	<h4>Los campos marcados con <span class="req">*</span>  son requeridos</h4>
  </div>
  

 <!-- la forma. ------>
  <div class="cajacentra">

    <form id="modifprod" action="<?php echo $_SERVER['PHP_SELF'];?>" method = "post">
        <div class="error" style="display:none;">
            <img src="img/warning.gif" alt="Warning!" width="24" height="24" style="float:left; margin: -5px 10px 0px 0px; " />
            <span ></span><br clear="all" />
        </div>
        
       <table  class="db-table">
             
            <tr>
                <td >No.</td> 
                <?php
                echo "<input type='hidden' id='num' name ='num' value = $num />";
                echo "<td>$num</td>";
                echo "<td><label for 'inic'>Descripcion</label></td>";
				echo "<td class='field'><input id='inic' name ='descr' value = '$descr'  size = '60'class = 'requer'/> 
				<span class='req'>*</span></td>";
				echo "<td><label for 'nombre'>Nombre Completo</label></td>";
				echo "<td class='field'><input name ='nombre' value = '$nombre' class='req' />
                <span class='req'>*</span></td>"; 
                echo "<tr>";
				echo "<td><label for 'corto'>Nombre Corto</label></td>";
				echo "<td class='field'><input name ='corto' value = '$nom_corto'  size = '30' class = 'requer'/> 
                <span class='req'>*</span></td>";
				echo "<td><label for 'prov'>Proveedor</label></td>";
               	echo "<td class='field'><input name ='prov' id= 'prov' value = '$proveedor' size = '30' class='ui-autocomplete-content' class='requer'/> 
               	<span class='req'>*</span></td>";
				echo "<input  type= 'hidden' name ='noprov' id= 'noprov' value = '$idprov'/>";
				echo "<td><label for 'codigo'>Codigo</label></td>";
                echo "<td class='field'><input name ='codigo' id='codigo' value = '$codigo' class='requer'/>
                <span class='req'>*</span></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td >Unidad:</td> ";
				echo "<td class='field'><input  name ='unidad' value = '$unidad'  size = '30'class='requer'/> 
                <span class='req'>*</span></td>";
				echo "<td >Cantidad:</td> ";
				echo "<td class='field'><input  name ='cantidad' value = '$cantidad'  size = '30'class='requer'/> 
                <span class='req'>*</span></td>";
                echo "<td ><label for 'precio1>Precio 1</label></td> ";
                echo "<td class='field' ><input name ='precio1' value = '$precio1' class='reqnum' />
                <span class='req'>*</span></td>";
				echo "<td >Precio 2</td> ";
           echo "</tr>";
		   echo "<tr>";
		   echo "<td class='field'><input name ='precio2' value = '$precio2' class='reqnum'/>
                <span class='req'>*</span></td>";
		        echo " <td>Precio 3</td>";
                echo "<td class='field'><input name ='precio3' value = '$precio3'class='reqnum' />
                <span class='req'>*</span></td>"; 
				echo " <td>Código de Barras</td>";
				echo "<td class='field'><input name ='cbarras' value = '$cbarras'class='req' />
                <span class='req'>*</span></td>"; 
				echo " <td>Grupo</td>";
				echo "<td class='field'><input name ='grupo' value = '$grupo' class='req' />
                <span class='req'>*</span></td>"; 
        	echo "</tr>";
		   	echo "<tr>";        
				
			echo "</tr>"; 
            ?>         
     </tr>
   
     
                      
          </table>  <br />
    <!--------el boton de enviar ------------->
    <div class="centraelem">
        <?php
           echo  "<input type='submit' name ='enviomod' value=$titbot />"
        ?>
    </div>        
        </form>
    

</div>

<div id="footer"></div>


</body>


</html>
