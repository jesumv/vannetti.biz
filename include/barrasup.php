
<!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->  
     <div id="bandasup">

	         <div id="div2">        
	         	<h3>fecha: <?php echo date("d-m-Y") ?></h3> 
	        </div> 

	       <div id= "saludo">
	       		<h3>Bienvenido(a), <?php echo $_SESSION['username']; ?></h3>
	       </div>

	 </div>

<p></p>
     
    
<!--INCLUSION DE LA BARRA DE MENU -->
<?php
    	include_once "menu1.php";
?>



<!--SECCION DE CONTENIDO-->
        
            <?php 
            echo "<h1 id='titpag' align='center'>";
                if(!isset($titulo)){
                   
                   echo "NO HAY TITULO PARA ESTA PAGINA" ;
                }
                else {
                 echo $titulo; 
                }
             echo "</h1>"  ; 
            ?>

        
 
     
        
        
        
