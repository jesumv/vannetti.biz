<?php
  function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
						 //asignacion de variables
					  $myusername = $_POST['username'];
					  $mypassword = $_POST['password'];
					  $sql=sprintf("SELECT id,username,empresa,nivel,nombre,email FROM usuarios WHERE username='$myusername' 
					                and passcode=(AES_ENCRYPT('%s','%s'))",$mypassword,$mypassword);
					  $result=mysqli_query($mysqli,$sql);
					                $row=mysqli_fetch_array($result);
					                $usuario = $row[1];
					                $empre = $row[2];
									$nivel =$row[3];
									$nombre =$row[4];
									$email=$row[5];
					                $count=mysqli_num_rows($result); 
					                $result->free();		                
					                $mysqli->close();
					  
					  //
						if($count == 1){
							session_start();
							$_SESSION['usuario'] = $usuario;
					        $_SESSION['nombre']=$nombre;
					        $_SESSION['nivel']=$nivel;
					        $_SESSION['empresa']=$empre;
							$_SESSION['uemail']=$email;
							$_SESSION['root'] = realpath($_SERVER["DOCUMENT_ROOT"]);
							if($nivel<10){
								echo "1";
							}else{echo "2";}
						} else {
							echo "0";	
						}
						exit();		

    } else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
	
 





