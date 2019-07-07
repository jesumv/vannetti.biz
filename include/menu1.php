	<div id="menuPositioner">
    <div id="menuHolder">
            <ul id="menu">
                <li><a class="fly"><b>Inicio</b></a>
                	<ul class="sub1">
                		<li><a href="portal.php">PC</a></li>
                		<li><a href="portalmov.php">Movil</a></li>
                	</ul>
                </li>
                <li class="currentsub"><a class="fly" >Consultas</a>
                	<ul class="sub1">
                		<li ><a href="inventarios.php">Inventarios</a></li>
                		<li ><a href="#">Facturación</a></li>
                		<li ><a href="#">Cobranza</a></li>
                		<li ><a href="#">Remisiones</a></li>
                	</ul>
                  </li>
                  <li class="currentsub"><a class="fly">Clientes</a>
                    	<ul class="sub1">
                    		<li ><a href="pedido.php">Pedidos</a></li>
                    		<li><a href="ventas.php">Registro ventas XML</a></li>
                    		<li><a href="mostrador.php">Vtas Mostrador</a></li>
	                        <li><a href="cxc.php">Cuentas por cobrar</a></li>
	                        <li><a href="regmues.php">Muestras</a></li>
	                        <li><a href="listasp.php">Listas de Precios</a></li>
                        </ul>

                   </li>
                    <li class="currentsub"><a class="fly">Proveedores</a>
                    	<ul class="fly2">
                    		<li ><a href="cxp.php">Cuentas por pagar</a></li>
                			<li class="currentfly"><a href="oc.php">Emisión OC</a></li>
                			<li><a href="listoc.php">Recepción OC</a></li>
                			<li><a href="pagooc.php">Pago OC</a></li>
                		</ul>
                	</li>
                    	
                    <li><a><b>Administración</b></a>
                        <ul class="sub1">
                        	<li><a href="reggasto.php">Gastos</a></li>
                            <li><a href="clientes.php">Clientes</a></li>
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
