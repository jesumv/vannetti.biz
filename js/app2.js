
(function() {
  'use strict';
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
  function llenaop(){
	  //esta funcion añade opciones a la lista de proveedores
	   			$.get('php/getprovs.php',function(data){
	 			var obj1 = JSON.parse(data);
	 			for( var z=0; z <obj1.length; z++) {
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

  document.getElementById('altaprod').addEventListener('click', function() {
    // Open/show the add new city dialog
	llenaop();
    app.toggleAddDialog(true);
    
  });

  document.getElementById('butAddCancel').addEventListener('click', function() {
	    // Open/show the add new city dialog
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
