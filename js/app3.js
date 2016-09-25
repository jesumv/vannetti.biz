  'use strict';
  
(function() {

  var app = {
		    isLoading: true,
		    spinner: document.querySelector('.loader'),
		    container: document.querySelector('.main'),
		    addDialog: document.querySelector('.dialog-container'),
		  };

  /*****************************************************************************
   *
   * Event listeners for UI elements
   *
   ****************************************************************************/
  /*****************************************************************************
  *
  *la promesa
  *
  ****************************************************************************/
  
  function llenaop(){
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
	 			
	   };
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
	var descsel = obj1[0].desc;
	var ivasel= converbool(obj1[0].iva);
	var pesosel=converbool(obj1[0].speso)
	var p1sel = obj1[0].pr1;
	var p2sel = obj1[0].pr2;
	var p3sel = obj1[0].pr3;
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
	var ivacas = document.getElementById('chiva');
	ivacas.checked = ivasel;
	var pesocas = document.getElementById('chpeso');
	pesocas.checked = pesosel;
	var descas =  document.getElementById('desc');
	descas.value = descsel;
	var p1cas =  document.getElementById('p1');
	p1cas.value = p1sel;
	var p2cas =  document.getElementById('p2');
	p2cas.value = p2sel;
	var p3cas =  document.getElementById('p3');
	p3cas.value = p3sel;
} 

function traeprod(indice){
	//recolectar los datos para el dialogo
	$.get('php/getprod.php',{idprod:indice},function(data){
			var obj1 = JSON.parse(data);
			llenacas(obj1);
			});
}
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
//escuchas del boton de alta
  document.getElementById('altaprodb').addEventListener('click', function(){
	  aparece(0)},false);
  //añadir escuchas a elementos editar
  	var editables = document.getElementsByClassName('ed');
  		for (var i = 0; i < editables.length; i++) {
  			editables[i].addEventListener('click', function(){aparece(this.id)}, false);
  		}

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
 }
  document.getElementById('butAddCancel').addEventListener('click', function() {
	  	quita();
	    // oculta el dialogo de datos de producto
	    app.toggleAddDialog(false);
	  }); 

  /*****************************************************************************
   *
   * Methods to update/refresh the UI
   *
   ****************************************************************************/

  // Toggles the visibility of the add new city dialog.
  app.toggleAddDialog = function(visible) {
    if (visible) {
      app.addDialog.classList.add('dialog-container--visible');
    } else {
      app.addDialog.classList.remove('dialog-container--visible');
    }
  };

  /*****************************************************************************
   *
   * Methods for dealing with the model
   *
   ****************************************************************************/




})();
