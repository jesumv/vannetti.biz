<?php
//ESTA HOJA MUESTRA CAMPOS DE CAPTURA PARA LA TABLA DE USUARIOS
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
    } else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
	

//alta del usuario en la base de datos

if(isset($_POST['enviou'])){
//VALIDACIONES

//CONVERSIONES
$nombre = $_POST['nombre'];
$user = mysqli_real_escape_string($mysqli,$_POST['usuario']) ;;

$pw=mysqli_real_escape_string($mysqli,$_POST['pw']) ;
$nivel = $_POST['nivel'];


//string de llenado de campos tabla admin
               $querya = sprintf("INSERT INTO usuarios (nombre,username,passcode,nivel) 
               VALUES ('$nombre','$user',(AES_ENCRYPT('%s','%s')),$nivel)",$pw,$pw);
         
//lenado de campos
               
                $resultal=mysqli_query($mysqli,$querya) or die("Error en alta usuario: ".mysqli_error());
				
                 if($resultal){
//el registro se inserto correctamente
                    echo '<script type="text/javascript">
                            window.alert("Usuario añadido correctamente!");
                        </script>';
                     
                  }
                 else{
                 //No se pudo lograr la insercion, crea una entrada en el log
                 
                        echo '<script type="text/javascript">
                            window.alert("Error. No se pudo dar de alta el usuario!");
                        </script>';
                        
                       echo "error en alta articulo".mysql_error(); 
                       creaLog(mysql_error());
                 }
    			  /* cerrar la conexi�n */
  					mysqli_close($mysqli);
				
}
    
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">   
<head>  
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<!--links a hojas de estilo ----------------------------------------------------->
	<link rel="shortcut icon" href="img/logomin.gif" />
	<link rel="stylesheet" type="text/css" href="css/inline.css">
	<link rel="stylesheet" type="text/CSS" href="css/dropdown_two.css" />
	<link rel="shortcut icon" href="img/logomin.gif" />  
    <link rel="apple-touch-icon" href="img/logomin.gif">
	
	<!-- links a hojas javascript ---------------------------------------------------->
	
	<title>Vannetti Consulenti</title>

</head>

<body>
  <!--LISTON DE ENCABEZADO ----------------------------------------------------------------------------------------> 
  
 <header class="header">
  	<div>
    	<h1 class="header__title">Bienvenido(a), <?php echo $_SESSION['nombre']; ?></h1>
    </div>
    
  </header>
<!--CONSTRUCCION DE LA PAGINA ----------------------------------------------------------------------------> 
<!--FORMA CON TABLA DE USUARIOS -------------------------------------------------------------------------->
<main class="main">
	 <br />
	 <h2>ADMINISTRACION DE USUARIOS</h2>
	  
	  <?php
	  		include_once "include/menu1.php";
	  ?>
	  
	  <form id="altausu" action="<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST">
	        <table border = "1" class="centraelem">
	            <tr>
	                <td class="celdacolor">NOMBRE</td>
	                <td><input name ='nombre'/> </td>
	            </tr>
	            <tr>
	                <td class="celdacolor">NIVEL</td>
	                <td><input name ='nivel'/> </td>
	            </tr>
	            <tr>
	                <td class="celdacolor">USUARIO</td>
	                <td><input name ='usuario'/> </td>
	            </tr>
	            <tr>
	                <td class="celdacolor">CONTRASEÑA</td>
	                <td><input type ="password" name ='pw'/> </td>
	            </tr>
	        </table>
	        <br />
	    <!--------el boton de enviar ------------->  
	    	<div class="centraelem">
	    		 <input type="submit" name ="enviou" value="Alta" /> 
	    	</div>     
	    </form>
  </main>
	<div id="footer"></div>

</body>

</html>
  
