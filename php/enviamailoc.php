<?php
	//cachar parametros
	$oc = $_POST['oc'];
	//consulta a la base de datos
	/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
	
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
    	/*** checa login***/
    	$funcbase->checalogin($mysqli);
		/*** datos orden de compra***/
		$sqlcom1= "SELECT idproveedores,fechamov FROM oc WHERE idoc=$oc";
		$query1=mysqli_query($mysqli, $sqlcom1)or die("error en cons oc: ".mysqli_error($mysqli));
		$fila=mysqli_fetch_array($query1);
		$idprov=$fila[0];
		$fechao=$fila[1];
		mysqli_free_result($query1);
		/*** datos proveedor***/
		$sqlcom2= "SELECT razon_social,email,contacto FROM proveedores WHERE idproveedores=$idprov";
		$query2=mysqli_query($mysqli, $sqlcom2)or die("error en cons oc: ".mysqli_error($mysqli));
		$fila2=mysqli_fetch_array($query2);
		$razons=$fila[0];
		$pemail=$fila2[1];
		$nombrec=$fila2[2];
		mysqli_free_result($query2);
		//variables para la tabla de productos
		$filas;
		$mensaje2="";
		/*** datos arts orden de compra***/
		$sqlcom3="SELECT t1.cant,t2.nombre FROM artsoc AS t1 INNER JOIN productos AS t2 on t1.idproductos=
		t2.idproductos WHERE idoc= $oc";
		$query3=mysqli_query($mysqli,$sqlcom3)or die("error en cons. arts oc:".mysqli_error($mysqli));
		if($query3){
			//recoleccion de variables
			$usu= $_SESSION['usuario'];
			$uemail=$_SESSION['uemail'];
		 //creacion de datos para el pedido
		 	$filas = $query3->num_rows;
			while($tempo=mysqli_fetch_array($query3, MYSQLI_ASSOC)){
				$mensaje2.="<tr><td style='width:500px'>".$tempo['nombre']."</td><td style='width:30px'>".$tempo['cant']."</td></tr>";
			 }
			mysqli_free_result($query3);
		 //conformacion del mensaje
   		$cabeceras = 'From: '.$uemail."\r\n" ;
   		$mensaje1="<div><p>Estimada(o) <b>".$nombrec."</b>:</p><p>Por este medio me permito solicitarte el siguiente PEDIDO, con ID No.$oc</p>
   		<p>consistente en los siguientes art&iacute;culos:</p></div><br><br>
   		<div><table border='1'><tr><th>PRODUCTO</th><th>CANTIDAD</th></tr>";
		$mensaje3="<div><p>Gracias Anticipadas por tu amable ayuda</p><br><br>
		<p style='color:blue; font-style:italic'>Jes&uacute;s Maldonado Vannetti</p>
		<p style='color:blue; font-style:italic'>Vannetti Cucina</p>
		</div>";
		$mensajef="<div>".$mensaje1.$mensaje2."</table></div></div><br><br>".$mensaje3;
   		//envio del mensaje
   		require 'phpmailer/PHPMailerAutoload.php';
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->IsHTML(true);
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 465;
			$mail->Username = 'jmaldonadoca@gmail.com';
			$mail->Password = 'E05101963';
			$mail->setFrom('ventas@vannetti.biz');
			$mail->addAddress($uemail);
			$mail->Subject = 'Orden de Compra Jesus Maldonado';
			$mail->Body = $mensajef;
			//send the message, check for errors
			if (!$mail->send()) {
			    $jsondata= "ERROR: " . $mail->ErrorInfo;
			} else {
				//actualizar estado de orden de compra
				$sqlCommand4 = "UPDATE oc SET status = 5  WHERE idoc = $oc";
				$query3 = mysqli_query($mysqli, $sqlCommand4) or die ('error en marcado de mail env '.mysqli_error($mysqli)); 
				if($query3){ $jsondata =0;}else{ $jsondata =-2;}		   
			}
		}else{$jsondata=-1;}; 	
   		//salida de respuesta
		 echo json_encode($jsondata);	
	}else{die ("<h1>'No se establecio la conexion a bd'</h1>");}
  
?>