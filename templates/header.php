<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
        <title><?php echo htmlspecialchars($title) ?></title>
        <link rel="stylesheet" href= "css/plant3.css" />
        <link rel="stylesheet" type="text/CSS" href="css/dropdown_twoV2.css" />
        <link rel="shortcut icon" href="img/logomin.gif" />  
  		<link rel="apple-touch-icon" href="img/logomin.gif">
        <script src="js/fcfdiv3.js"></script>
        <script src="js/fauxfact.js"></script>
        <script src="js/jquery3/jquery-3.3.1.min.js"></script>
        <script type="text/javascript">
        (function(){
        	'use strict';
        	var datosg={};
        	$(document).ready(function(){
        		//definicion de objetos
        		function ventatext(vtaint){
                		var tresp;
                		switch(vtaint){
                		case "0":
                    		tresp="EFECTIVO MOSTRADOR";
                    	break;
                		case "1":
                			tresp="CONTADO BANCOS";
                        break;
                        default:
                        	tresp="CREDITO CXC";
                		}
                		return tresp;
            		}
        		function regped(){
            		//registra pedido x vta xml
        			var arche=document.getElementById("archpd").value;
        			if(arche!=""){
            			var fechan=document.getElementById("fechafact");
            			datosg.fecha=fechan.value;
            			datosg.tipoventa=document.getElementById("fpago").value;
            			datosg.arch=arche.substr(14);
            			var datosr=JSON.stringify(datosg);
        				ajaxcall("php/enviaventas.php",datosr).then (function(response){
   					   	 	return JSON.parse(response)}).then(function(response){
 					    	var resul=response.result2;
 		            		var pedido=response.ped;
 		            		var tventa=response.tventa;
 		            		var smensaje;
 		            		var tmens=ventatext(tventa);
 		            		switch(resul){
 		            		case 0:
 	 		            		smensaje= "VENTA REGISTRADA OK. PEDIDO: "+pedido
 	 		            		+" "+tmens;
 	 	 		            		
 	 		            	break;
 		            		case "-1":
 		            			smensaje= "ERROR EN REGISTRO PEDIDO";
 	 	 		            break;
 		            		case "2":
 		            			smensaje= "ERROR EN ARTS PEDIDO";
 	 	 	 		            break;
 		            		case "3":
 		            			smensaje= "ERROR EN REGISTRO INVENTARIO";
 	 	 	 		            break;
 		            		case "4":
 		            			smensaje= "ERROR EN MOVTOS DIARIO";
 	 	 	 		            break;
 		            		case "99":
 		            			smensaje= "ERROR EN CONEXION A BD";
 	 	 	 		            break;
 		            		case "1000":
 		            			smensaje= "ERROR EN ENVIO DATOS";
 	 	 	 		            break;
 	 	 	 		        default:
 	 		            		smensaje="ERROR NO DEFINIDO";
 		            		}

 		            		presentaerr(smensaje);
 					    	},function(error){
    						  	presentaerr("FALLO EN CONSULTA AJAX", error);
  						  })
        			}else{presentaerr("SELECCIONE UN ARCHIVO!")}
        			
        			
        		}
        		   function generaped(contenido){
        				 //extraer conceptos
        				   	var rfcrecep=contenido.rfcrecep;
        				   	var datos={"rfc":rfcrecep};
        				   	var datosr=JSON.stringify(datos);
        				   	var cadenap= "php/consultascfdi.php";
        				   	var resul;
        				   	var nomcorto;
        				   	var idcte;
        				   	const divped=document.getElementById("pedido");
        				   	ajaxcall(cadenap,datosr).then (function(response){
        					   	 return JSON.parse(response)}).then(function(response) {
        					    	resul=response.exito;		    	
        					//validar respuesta del servidor
        							switch(resul){
        							case -1:
        								//error en consulta php
        								presentaerr(response.error)
        								break;
        							case 0:
        								//exito
        								nomcorto = response.nomcorto;
        								idcte= response.idctes;
        								var nfecha = new Date(contenido.fecha).toISOString().slice(0,10)
        								//poblar el objeto de salida
        								datosg.cte=idcte;
        								datosg.facturarp="true";
        								datosg.sefo=contenido.seriefolio;
        								datosg.prods=hazarts(contenido.conceptos);
        								datosg.totarts=datosg.prods.length;
        								datosg.montot=contenido.stotal;
        								datosg.totiva=contenido.iva;
 										datosg.totieps=contenido.ieps;
        								datosg.total=contenido.total
        								//construir pedido
        								cabezaped(nomcorto,idcte,contenido.seriefolio,nfecha);
        								cuerpoped(contenido);
        							break;
        							case 1:
        								//cliente no en base de datos.
        								presentaerr("CLIENTE NO EN BD");
        								var arche =document.getElementById("archpd");
        								arche.value="";
        								break;
        							default:
        							}			
        					  },function(error){
        						  	console.error("FALLO EN CONSULTA AJAX", error);
        						  })		
        				   	
        				   }
        		var app ={
        				 container: document.querySelector('body'),
        				 avisoDialog: document.querySelector('.dialog-container'),
        				 aviso:document.querySelector('#aviso')
        		}
        		  /*****************************************************************************
        		   *
        		   * Methods to update/refresh the UI
        		   *
        		   ****************************************************************************/
           		
        		   // Toggles the visibility of the dialog.
        		   app.toggleDialog = function(visible) {
        		     if (visible) {
        		       app.avisoDialog.classList.add('dialog-container--visible');
        		     } else {
        		       app.avisoDialog.classList.remove('dialog-container--visible');
        		       var errreng=document.getElementById("errmens");
        		       errmens.innerHtML="";
        		     }
        		   };
        		   app.menserr= function(mensaje){
        			   //muestra mensaje en dialogo
        			   var inserta= document.getElementById("errmens");
        			   inserta.innerHTML=mensaje;
        		   }

        		   function presentaerr(perror){
        			   //presenta dialogo de error
        			   app.toggleDialog(true);
        				 app.menserr(perror);
        			   }
       			function creapag(e,callback,callback2){
    				var files = e.target.files; // FileList object
    				var resulta;
    				var f=files[0];
    				var r = new FileReader();
    			      	r.onload = (function(f){
    			      		 return function(e){
    			      			var contents=e.target.result;
    							 resulta=callback(contents); 
    							 if(resulta.exito===0){
    								 callback2(resulta);
    							 }else{
    							 presentaerr(resulta.error);
    							 document.getElementById("files").value = "";
    								 }
    				     		  
    			      		}
    			      	})(f);
    					r.readAsText(f);
    
    				}
     		  var gped = document.getElementById('archpd');
     		  	gped.addEventListener('change', function(e){creapag(e,leeXMLing,generaped)},false);
     		 var botreg = document.getElementById('botreg');
      			botreg.addEventListener('click',regped,false);
     		 var butok = document.getElementById('butok');
     			butok.addEventListener('click', function(){app.toggleDialog(false);deleteChild()},false);
    		});
		})();
        </script>
    </head>
         <header class="cabezal">
         	<h1 class="header__title">Bienvenido(a), <?php echo $_SESSION['nombre']; ?></h1><br/>
         </header>         
<body>
