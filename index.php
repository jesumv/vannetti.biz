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
  <link rel="shortcut icon" href="img/logomin.gif" />  
  <link rel="apple-touch-icon" href="img/logomin.gif">
 <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script type="text/javascript" src="js/login.js"></script>
</head>
<style>
	body{
    	background: url("img/logocucina.jpg") no-repeat scroll 0 0 transparent;
	}
</style>
<body>

  <header class="header">
    <h1 class="header__title">Vannetti Inc.</h1>
    <button id="primero" >ENTRAR</button>
  </header>
  

  <main class="main">
  	<br />
  <h2>  VANNETTI CUCINA</h2>
  </main>

  <div class="dialog-container">
      <div class="dialog">
      <div class="dialog-title">Proporcione su Datos</div>
      <div class="dialog-body">
        <!-- la caja para registro de usuario -->
        <form id="board_login" name="board_login" method ="post" action="#" onsubmit="return false;">
        	<div> <label>Usuario  :</label><input type="text" name="username" id="username" /></div>
           <div><label>Contraseña :</label><input type="password" name="password" id="password"/></div>
            <div><input type="submit" value=" Enviar "/><br />   </div>      
      </div>
      <div class="dialog-buttons">
      </div>
      </form>
        <button id="butAddCancel" class="button">Cancelar</button>
    </div>
  </div>

  <!-- Insert link to app.js here -->
  <script src="js/app.js"></script>
  
</body>
</html>

