<?php
  function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
/*** checa login***/
        //$funcbase->checalogin($mysqli);
    } else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
	
  //asignacion de variables
  $myusername = $_POST['username'];
  $mypassword = $_POST['password'];
  
  $sql=sprintf("SELECT id,username,empresa,nivel FROM usuarios WHERE username='$myusername' 
                and passcode=(AES_ENCRYPT('%s','%s'))",$mypassword,$mypassword);
  $result=mysqli_query($mysqli,$sql);
                $row=mysqli_fetch_array($result);
                $nivel =$row[3];
                $username = $row[1];
                $empre = $row[2];
                $count=mysqli_num_rows($result);
                
                $result->free();
                
                $mysqli->close();
  
  //
	if($count == 1){
		$_SESSION['tep_username'] = $username;
		$_SESSION['login_user']=$myusername;
        $_SESSION['nombre']=$username;
        $_SESSION['nivel']=$nivel;
        $_SESSION['empresa']=$empre;
		echo 1;
	} else {
		echo 0;	
	}
	exit();





