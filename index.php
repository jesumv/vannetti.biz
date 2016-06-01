<?php
    /*** Autoload class files ***/
    function __autoload($class){
      require('include/' . strtolower($class) . '.class.php');
    }
    //directiva a la conexion con base de datos
    $funcbase = new dbutils;
    $mysqli = $funcbase->conecta();
	
 /*** si se establecio la conexion***/
    if (is_object($mysqli)) {
        session_start();

        $error = "";

            if($_SERVER["REQUEST_METHOD"] == "POST")
                {
                // username and password sent from form 
                $myusername=mysqli_real_escape_string ($mysqli,$_POST['username']);
                $mypassword=mysqli_real_escape_string($mysqli,$_POST['password']); 
                $sql=sprintf("SELECT id,nombre,empresa,nivel FROM usuarios WHERE username='$myusername' 
                and passcode=(AES_ENCRYPT('%s','%s'))",$mypassword,$mypassword);
                $result=mysqli_query($mysqli,$sql);
                $row=mysqli_fetch_array($result);
                $nivel =$row[3];
                $username = $row[1];
                $empre = $row[2];
                
                $count=mysqli_num_rows($result);
                
                $result->free();
                
                $mysqli->close();
                
                // If result matched $myusername and $mypassword, table row must be 1 row
                if($count==1)
                {
                    
                $_SESSION['login_user']=$myusername;
                $_SESSION['username']=$username;
                $_SESSION['nivel']=$nivel;
                $_SESSION['empresa']=$empre;
                
                //seleccion de hoja segun empresa
                    switch ($empre) {
                        case 0:
                            header("location: portal.php");
                            break;
                        
                        default:
                             header("location: php/logout.php");
                            break;
                    }
                
            
            }
        //los datos de acceso no son correctos    
        else 
            {
                $error="Su nombre de usuario o contraseña son inválidos";
            }
        }
        
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
</head>
<body>

  <header class="header">
    <h1 class="header__title">La red de Vannetti</h1>
    <div id="loginbox">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <label>Usuario  :</label><input type="text" name="username" class="box"/>
                        <label>Contraseña :</label><input type="password" name="password" class="box" />
                        <input type="submit" value=" Enviar "/><br />
                    </form>
                    
                        <div style="font-size:16px; color:#cc0000; margin-top:10px" align="center"> <?php echo $error; ?></div>
                
                </div>
  </header>

  <main class="main">
    <!-- Insert forecast-card.html here -->
  <h2>  VANNETTI CONSULENTI</h2>
  </main>

  <div class="dialog-container">
    <!-- Insert add-new-city-dialog.html here -->
  </div>

  

  <!-- Insert link to app.js here -->
  
</body>
</html>

