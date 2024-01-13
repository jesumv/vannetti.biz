<?php 
    require_once('include/helpers.php');
    /*** Autoload class files ***/
    function myAutoload($ClassName)
    {
        require('include/' . strtolower($ClassName) . '.class.php');
    }
    
    spl_autoload_register('myAutoload');
    
    $funcbase = new dbutils;
    /*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
        /*** checa login***/
        $funcbase->checalogin($mysqli);
    }
    render('header', array('title' => 'Vannetti.biz V3.0'));
    render('menu',array('subtitulo' => 'REGISTRO DE VENTA XML','tboton' => 'Seleccione Archivo Factura',
        'tboton2' => 'Registrar Pedido')   );
?>
    <div id="empaque">
     	<div id="pedido" class="cuerpo">
    	</div>
    </div>	
   
 	<div id="pie"></div>	
    <div class='dialog-container'> 
    	<div id="aviso" class='dialog'>
    		<div class="dialog-title">AVISO:</div>
    		<div class="dialog-body" id="errmens"></div>
    		<div class="dialog-buttons">
        		<button id="butok" class="button">OK</button>
      		</div>
    	</div>
	</div>
<?php render('footer'); ?>