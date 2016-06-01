<div id="menuPositioner">
    <div id="menuHolder">
            <ul id="menu">
                <li><a href="portal.php"><b>Inicio</b></a></li>
                <li class="currentsub"><a class="fly" >Consultas</a>
                	<ul class="sub1">
                		<li ><a href="inventarios.php">Inventarios</a></li>
                		<li ><a href="consfactura.php">Facturación</a></li>
                		<li ><a href="consblanco.php">Rem. en blanco</a></li>
                		<li ><a href="constodas.php">Remisiones</a></li>
                	</ul>
                  </li>
                  <li><a>Almacén</a>
                    	<ul class="sub1">
	                        <li><a href="entradas.php">Entradas</a></li>
	                        <li class="currentsub"><a class="fly">Salidas</a>
	                            <ul class="fly2">
	                            	<li><a href="salidafact.php">Facturación</a></li>
	                            	<li ><a href="remisionesvend.php">Rem Agentes</a></li>
	                                <li class="currentfly"><a href="remisiones.php">Rem Clientes</a></li>
	                                <li><a href="remblanco.php">Rem En Blanco</a></li>
	                                <li><a href="remmanual.php">Rem Manuales</a></li>
	                            </ul>
	                        </li>
                        </ul>

                   </li>
                    <li class="currentsub"><a class="fly">Ventas</a>
                    	<ul class="fly2">
                			<li class="currentfly"><a href="oc.php">Ordenes de compra</a></li>
                		</ul>
                	</li>
                    	
                    <li><a><b>Administración</b></a>
                        <ul class="sub1">
                            <li><a href="clientes.php">Clientes</a></li>
                            <li><a href="proveedores.php">Proveedores</a></li>
                            <li><a href="productos.php">Productos</a></li>
                            <li><a href="representantes.php">Representantes</a></li>
                            <li><a href="sucursales.php">Sucursales</a></li>
                            <?php
                            if($_SESSION['nivel']==1){
                            	echo "<li><a href='admonusu.php'>Usuarios</a></li>";	
                            }
							?>
                                         
                        </ul>
                    </li>
                    <li><a href="php/logout.php"><b>Salir</b></a></li>
            </ul>
	</div>
</div>