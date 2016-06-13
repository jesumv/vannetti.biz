
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

  document.getElementById('primero').addEventListener('click', function() {
    // Open/show the add new city dialog
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
