<?php
  function __autoload($class){
	  require('include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
    if (is_object($mysqli)) {
/*** checa login***/
       $funcbase->checalogin($mysqli);
    } else {
        //die ("<h1>'No se establecio la conexion a bd'</h1>");
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
   <!-- Insert link to styles here -->
  <link rel="stylesheet" type="text/css" href="css/inline.css">
  <link rel="stylesheet" type="text/CSS" href="css/dropdown_two.css" />
  <link rel="stylesheet" type="text/css" href="css/plant1.css">
  <link rel="shortcut icon" href="img/logomin.gif" />  
  <link rel="apple-touch-icon" href="img/logomin.gif">
  <link rel="stylesheet" href="css/jquery-ui.min.css">
  <script src="js/jquery.js"></script>
  <script src="js/jquery-ui.min.js"></script>
 <script>
 'use strict';
 (function() {
	
	function traeprod(indice){
	//recolectar los datos para el dialogo
	$.get('php/getprod.php',{idprod:indice},function(data){
			var obj1 = JSON.parse(data);
			llenacas(obj1);
			});
	}

	function converbool(valor){
		var resp;
		//esta funcion convierte el valor numerico una variable booleana a texto para su manipulacion con js
		if(valor==1){resp=true}else{resp=false}
		return resp;
	};


	function llenacas(obj1)	{
		//extraccion de datos del array
		var idprod = obj1[0].idprod;
		var idprov = obj1[0].idprov;
		var grupo = obj1[0].grupo;
		var nombresel = obj1[0].nombre;
		var ncortosel = obj1[0].nomcorto;
		var ncatsel = obj1[0].nomcat;
		var codigosel = obj1[0].codigo;
		var udsel = obj1[0].unidad;
		var cantsel = obj1[0].cant;
		var barrasel = obj1[0].cbarras;
		var costosel = obj1[0].costo;
		var costovsel= obj1[0].costov;
		var descsel = obj1[0].desc;
		var ivasel= converbool(obj1[0].iva);
		var pesosel=converbool(obj1[0].speso)
		var pesoselv=converbool(obj1[0].spesov)
		var p1sel = obj1[0].pr1;
		var p2sel = obj1[0].pr2;
		var p3sel = obj1[0].pr3;
		var p4sel = obj1[0].pr4;
		//lenado de datos
		var idsel = document.getElementById('idprod');
		idsel.value= idprod;
		var provsel = document.getElementById('selectmenu');
		provsel.value= idprov;
		var gruposel = document.getElementById('selectmenu2');
		gruposel.value= grupo;
		var nombrecas =  document.getElementById('nombre');
		nombrecas.value = nombresel;
		var ncortocas =  document.getElementById('nomcor');
		ncortocas.value = ncortosel;
		var ncatcas =  document.getElementById('nomcat');
		ncatcas.value = ncatsel;
		var codigocas =  document.getElementById('cod');
		codigocas.value = codigosel;
		var udcas = document.getElementById('selectmenu3');
		udcas.value= udsel;
		var cantcas =  document.getElementById('cant');
		cantcas.value = cantsel;
		var barrascas =  document.getElementById('barr');
		barrascas.value = barrasel;
		var costocas =  document.getElementById('cost');
		costocas.value = costosel;
		var costovcas =  document.getElementById('costv');
		costovcas.value = costovsel;
		var ivacas = document.getElementById('chiva');
		ivacas.checked = ivasel;
		var pesocas = document.getElementById('chpeso');
		pesocas.checked = pesosel;
		var pesocasv = document.getElementById('chpesov');
		pesocasv.checked = pesoselv;
		var descas =  document.getElementById('desc');
		descas.value = descsel;
		var p1cas =  document.getElementById('p1');
		p1cas.value = p1sel;
		var p2cas =  document.getElementById('p2');
		p2cas.value = p2sel;
		var p3cas =  document.getElementById('p3');
		p3cas.value = p3sel;
		var p4cas =  document.getElementById('p4');
		p4cas.value = p4sel;
	}
	
	function validar(){
		if($("#selectmenu").val() === '0'){
			alert("Debe seleccionar un proveedor");
			$("#selectmenu").focus();
			return false;
		}
		if($("#selectmenu2").val() === '0'){
			alert("Debe seleccionar un grupo");
			$("#selectmenu2").focus();
			return false;
		}
		if($("#nombre").val() === ''){
			alert("Debe proporcionar un nombre");
			$("#nombre").focus();
			return false;
		}
		if($("#nomcor").val() === ''){
			alert("Debe proporcionar un nombre corto");
			$("#nomcor").focus();
			return false;
		}
		
		if($("#nomcat").val() === ''){
			alert("Debe proporcionar un nombre de catalogo");
			$("#nomcat").focus();
			return false;
		}
		
		if($("#cod").val() === ''){
			alert("Debe proporcionar un codigo de producto");
			$("#cod").focus();
			return false;
		}
		if($("#selectmenu3").val() === '0'){
			alert("Debe seleccionar una unidad");
			$("#selectmenu3").focus();
			return false;
		}
		if($("#cant").val() === ''){
			alert("Debe proporcionar la cantidad");
			$("#cant").focus();
			return false;
		}

		if($("#cost").val() === ''){
			alert("Debe proporcionar el costo del producto");
			$("#cost").focus();
			return false;
		}
		if($("#costv").val() === ''){
			alert("Debe proporcionar el costo de venta del producto");
			$("#costv").focus();
			return false;
		}
		
		if($("#desc").val() === ''){
			alert("Debe proporcionar la descripción");
			$("#desc").focus();
			return false;
		}
		if($("#p1").val() === ''){
			alert("Debe Proporcionar al menos un precio de producto");
			$("#p1").focus();
			return false;
		}
		return true;
	}
	
	function traeprovs(){
		 //esta funcion añade opciones a la lista de proveedores
	   			$.get('php/getprovs.php',function(data){
	 			var obj1 = JSON.parse(data);
	 			for( var z=0 ; z< obj1.length; z++) {
	 //extraccion de datos del array
	 				var id = obj1[z].id;
	 				var nombre = obj1[z].nombre;		
	 //adicion de opciones select
	 			  	var men = document.getElementById('selectmenu');
	 				var option = document.createElement("option");
	 				option.text = nombre;
	 				option.value = id;
	 				men.add(option);		
	 			};
	 		});
	}
	
	function traegrupo(){
			 //y a la lista de grupos
	   			$.get('php/getgrupo.php',function(data){
	 			var obj2 = JSON.parse(data);
	 			for( var z2=0; z2 <obj2.length; z2++) {
	 //extraccion de datos del array
	 				var idg = obj2[z2].idg;
	 				var nombreg = obj2[z2].nombreg;		
	 //adicion de opciones select
	 			  	var men = document.getElementById('selectmenu2');
	 				var option = document.createElement("option");
	 				option.text = nombreg;
	 				option.value = idg;
	 				men.add(option);
	 		
	 		};
	 		});
	}
	
	function traeuds(){
			 //y a la lista de unidades
	   			$.get('php/getunid.php',function(data){
	 			var obj3 = JSON.parse(data);
	 			for( var z3=0; z3 <obj3.length; z3++) {
	 //extraccion de datos del array
	 				var idu = obj3[z3].idu;
	 				var nombreu = obj3[z3].nombreu;		
	 //adicion de opciones select
	 			  	var men = document.getElementById('selectmenu3');
	 				var option = document.createElement("option");
	 				option.text = nombreu;
	 				option.value = idu;
	 				men.add(option);	
	 		};
	 		});
	}
	
 	  function llenaop(){
 	  	var opaso1 = new Promise(function(resolve,reject){
			traeprovs();
		});
			opaso1.then(traegrupo());
		 	opaso1.then(traeuds());		
	   };
	 
	 

	  function quita(){
	  		//quitar proveedores
		 var provs = document.getElementById('selectmenu');
		 var no = provs.length;
		 for(var i=1; i<no; i++) {
			 provs.remove(1);
		 }
		 //quitar grupos
		 var grupos = document.getElementById('selectmenu2');
		 var no2 = grupos.length;
		 for(var i=1; i<no2; i++) {
			 grupos.remove(1);
		 }
		//quitar unidades
		 var uds = document.getElementById('selectmenu3');
		 var no3 = uds.length;
		 for(var i=1; i<no3; i++) {
			 uds.remove(1);
		 }
		 //limpiar inputs
		document.getElementById("altaprod").reset();
	 }
	  $(document).ready(function() {
	  		 var app = {
			    isLoading: true,
			    spinner: document.querySelector('.loader'),
			    container: document.querySelector('.main'),
			    addDialog: document.querySelector('.dialog-container'),
		  };
		  
		 //escucha del boton de alta
	  		document.getElementById('altaprodb').addEventListener('click', function(){
	  		aparece(0);	
		  },false)
		  
			  function aparece(indice){
					var datosprod = {} ;
					// esta funcion muestra la caja de dialogo de producto
					if(indice !== 0){
						var prodsel = new Promise(function(resolve,reject){
							llenaop();
						})
					//recolectar los datos para el dialogo y llenar las casillas
						prodsel.then(datosprod = traeprod(indice))
						prodsel.then (app.toggleAddDialog(true));	
						}else{
								var prodvac = new Promise(function(resolve,reject){
									llenaop();
								});
								prodvac.then(app.toggleAddDialog(true));
							};
				
				
				}; 
				function modificab(){
				 	//esta funcion añade escuchas a botones ed
				 	var editables = document.getElementsByClassName('ed');
				  	for (var i = 0; i < editables.length; i++) {
				  			editables[i].addEventListener('click', function(){aparece(this.id)}, false)
				  		}
				 }
			  	//escuchas de botones modificar
			  	modificab();
  		
			  $(document).on("submit","#altaprod", function(){
	  					if(validar){
							$.post('php/altaprod.php', $(this).serialize(), function(data){
							if(data == -1){
								alert("error en movimiento de producto");
							} else if (data == 0){
								alert("alta de producto OK");
								location.reload(); 	
							}else if (data == 1){
								alert("cambio al producto OK");
								location.reload(); 	
								}
							});
						}
					})
					
   /*****************************************************************************
   *
   * Methods to update/refresh the UI
   *
   ****************************************************************************/
			  	 // Toggles the visibility of dialog.
			  	 
				  app.toggleAddDialog = function(visible) {
				    if (visible) {
				      app.addDialog.classList.add('dialog-container--visible');
				    } else {
				      app.addDialog.classList.remove('dialog-container--visible');
				    }
				  }; 
				  
				   if (app.isLoading) {
				      app.spinner.setAttribute('hidden', true);
				      app.container.removeAttribute('hidden');
				      app.isLoading = false;
			    }
				  
				 document.getElementById('butAddCancel').addEventListener('click', function() {
			  	quita();
			    // oculta el dialogo de datos de producto
			    app.toggleAddDialog(false);
			  },false); 
			   		
	        }); 	        
  })();
  </script>  

</head>
<body>

  <header class="header">
    <h1 class="header__title">Bienvenido(a), <?php echo $_SESSION['nombre']; ?></h1>
  </header>
  <main class="main">
 	<br />
 <h2>CATALOGO DE PRODUCTOS</h2>
   <button type="button" id="altaprodb">ALTA PRODUCTO</button>
  	<?php
/*menu de navegación*/
include_once "include/menu1.php";   	

//-----CONSTRUCCION DE LA TABLA------------------------------------------------------------------------
 $table = 'productos';
 $table2 = 'grupos';
 $table3='unidades';
 $sql= "SELECT t2.nombre, t1.idproductos, t1.codigo, t1.cbarras,t1.nombre,t3.nombre, t1.cant,
 t1.costo, t1.precio1, t1.precio2,t1.precio3,t1.precio4 FROM $table AS t1 INNER JOIN $table2 AS t2 
 ON t1.grupo=t2.idgrupos INNER JOIN $table3 AS t3 ON t1.unidad=t3.idunidades 
 WHERE t1.status < 2 ";
 
 $result2 = mysqli_query($mysqli,$sql);

    if(mysqli_num_rows($result2)) {
        echo '<table cellpadding="0" cellspacing="0" class="db-table">';
        echo '<tr><th>Editar</th><th>Eliminar</th><th>Grupo</th><th>No.</th><th>Código</th><th>CBarras</th>
        <th>Producto</th><th>Unidad</th><th>Cantidad</th><th>Costo</th><th>Precio 1</th><th>Precio 2</th><th>Precio3</th><th>Precio4</th></tr>';
        //inicializacion de contador de renglon
        $reng = 1;
        while($row2 = mysqli_fetch_row($result2)) {
            $id = $row2[1];
            $elid = -$row2[1];
            echo '<tr id=r'.$id.' class="fila">';
            echo '<td ><a id='.$id.' class= "ed" href="javascript:void(0);"><img src="img/edita.jpg" ALT="editar"></a></td>';
            echo '<td class = el id='.$elid.'><a href ="elimprod.php?nid='.$elid.'"><img src="img/elimina.jpg" ALT="eliminar"></a></td>';
            foreach($row2 as $key=>$value) {
                echo '<td>',$value,'</td>';
            }
            echo '</tr>';
        $reng= $reng++;
        }
        echo '</table><br />';
    }else{echo '<h2>No hay productos a mostrar</h2>';}
 
 
  /* liberar la serie de resultados */
  mysqli_free_result($result2);
  /* cerrar la conexi�n */
  mysqli_close($mysqli);
  
  ?> 
  </main>
  
  <div class="dialog-container">
    <!-- dialogo para alta de producto -->
          <div class="dialog">
      		<div class="dialog-title">Datos del Producto</div>
		      <div class="dialog-body">
		        <form id="altaprod" name="altaprod" method ="post" action="#" onsubmit="return false;">
		        	<input type="hidden" name="idprod" id ="idprod" value="0"/>
		        	<div class="rengn">
		        		<label>Proveedor:</label><select id="selectmenu" name="selectmenu">
											<option value="0">Seleccione al proveedor</option>
         								</select>
         				<label>Grupo:</label><select id="selectmenu2" name="selectmenu2">
											<option value="0">Seleccione el grupo de productos</option>
         								</select>	
		        	</div>
		            <div class="rengn">
		            	<label>Nombre:  </label><input type="text" name="nombre" id="nombre" class="cajal"/>
		            </div>

		            <div class="rengn">
		            	<label>N. Corto:  </label><input type="text" name="nomcor"  id="nomcor" class="cajam"/>
		            	<label>N. Cat.:  </label><input type="text" name="nomcat"  id="nomcat" class="cajam"/>
		            </div>
		           <div class="rengn">
		           	<label>Unidad:    </label><select id="selectmenu3" name="selectmenu3" style="margin-right: 50px">
											<option value="0">Seleccione unidad medida</option>
         								</select>
		           	 <label>Cantidad:  </label><input type="text" name="cant"  id="cant" class="cajac" />
		           </div> 
		           <div class="rengn">
		            	<label>C Barras:  </label><input type="text" name="barr"  id="barr" class="cajam"/>
		            	<label>Código:  </label><input type="text" name="cod"  id="cod" class="cajac"/>
		            </div>
		            <div class="rengn">
		            	<label>Descripción:</label><input type="text" name="desc"  id="desc" class="cajal"/>
		            </div> 
		            <div class="rengn">
		            	<label>Costo:</label><input type="text" name="cost"  id="cost" class="cajac"/>
		            	<label>Costo de Ventas:</label><input type="text" name="costv"  id="costv" class="cajac"/>
		            </div>
		            <div class="rengn">
		            	<label>Costo según peso?</label><input type="checkbox" id="chpeso" name="chpeso" />
		            	<label>Venta según peso?</label><input type="checkbox" id="chpesov" name="chpesov" />
		            	<label>Causa Iva?</label><input type="checkbox" id="chiva" name="chiva" />
		            </div>
		            <div class="rengn">
		            	<label>P1 </label><input type="text" name="p1"  id="p1" class="cajac"/>
		            	<label>P2 </label><input type="text" name="p2"  id="p2" class="cajac"/>
		            	<label>P3 </label><input type="text" name="p3"  id="p3" class="cajac"/>
		            	<label>P4 </label><input type="text" name="p4"  id="p4" class="cajac"/>
		            </div>        
			   </div>
				      <div class="dialog-buttons">
				      	<input class="button a" type="submit" value=" Enviar "/>
				      	<button id="butAddCancel" class="button b" style="margin-left: 5px">Cancelar</button>
				      </div>
		      		   
		      	</form>
        
    </div>
  </div>
  
  <div class="loader">
    <svg viewBox="0 0 32 32" width="32" height="32">
      <circle id="spinner" cx="16" cy="16" r="14" fill="none"></circle>
    </svg>
  </div>
  
</body>
<div id="footer"></div>
</html>

