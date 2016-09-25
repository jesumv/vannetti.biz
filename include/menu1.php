	<div id="menuPositioner">
    <div id="menuHolder">
            <ul id="menu">
                <li><a href="portal.php"><b>Inicio</b></a></li>
                <li class="currentsub"><a class="fly" >Consultas</a>
                	<ul class="sub1">
                		<li ><a href="#">Inventarios</a></li>
                		<li ><a href="#">Facturación</a></li>
                		<li ><a href="#">Cobranza</a></li>
                		<li ><a href="#">Remisiones</a></li>
                	</ul>
                  </li>
                  <li class="currentsub"><a class="fly">Clientes</a>
                    	<ul class="sub1">
                    		<li ><a href="listasp.php">Listas de Precios</a></li>
	                        <li><a href="#">Salidas</a></li>
	                        <li ><a href="#">Remisiones</a></li>
	                        <li><a href="#">Muestras</a></li>
	                        <li><a href="#">Producción</a></li>
                        </ul>

                   </li>
                    <li class="currentsub"><a class="fly">Proveedores</a>
                    	<ul class="fly2">
                			<li class="currentfly"><a href="oc.php">Emisión OC</a></li>
                			<li class="currentfly"><a href="recoc.php">Recepción OC</a></li>
                			<li class="currentfly"><a href="pagooc.php">Pago OC</a></li>
                		</ul>
                	</li>
                    	
                    <li><a><b>Administración</b></a>
                        <ul class="sub1">
                            <li><a href="#">Clientes</a></li>
                            <li><a href="proveedores.php">Proveedores</a></li>
                            <li><a href="productos.php">Productos</a></li>
                            <?php
                            if($_SESSION['nivel']==1){
                            	echo "<li><a href='admonusu.php'>Usuarios</a></li>";	
                            }
							?>
                                         
                        </ul>
                    </li>
                    <li><a href="index.php"><b>Salir</b></a></li>
            </ul>
	</div>
</div>
