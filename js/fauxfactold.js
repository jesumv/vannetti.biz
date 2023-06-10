/**
 * @author jmv
 * este script contiene las funciones de presentacion de elementos
 * para facturas
 */
function deleteChild() { 
        var e = document.getElementById("pedido"); 
        //e.firstElementChild can be used. 
        var child = e.lastElementChild;  
        while (child) { 
            e.removeChild(child); 
            child = e.lastElementChild; 
        } 
        document.getElementById("archpd").value="";
    } 

	function hazarts(prods){
		//construye arreglo arts para artsped
		var arts=[];
		var prodlong=prods.length;
		for(var i=0;i<prodlong;i++){
			var presp= 1;
			var ivap;
			var iepsp;
			var cantprod=parseFloat(prods[i].getAttribute("Cantidad"));
			var idprod=prods[i].getAttribute("NoIdentificacion");
			var prprod=prods[i].getAttribute("ValorUnitario");
			var montop=prods[i].getAttribute("Importe");
			//se revisa si el concepto trae impuesto
			if(prods[i].children[0]){	
				var impuesto= prods[i].children[0].children[0].children[0].getAttribute("Impuesto");
				if(impuesto==="002"){
					 ivap = prods[i].children[0].children[0].children[0].getAttribute("Importe");	
				}else if(impuesto==="003"){iepsp= prods[i].children[0].children[0].children[0].getAttribute("Importe");}else{ivap="0";iepsp="0";};
				arts.push([idprod,cantprod,prprod,montop,ivap,presp,iepsp]);
			}
		}
		return arts;
	}
	
	function ajaxcall(url,datos){
		//regresar una promesa nueva
				return new Promise(function(resolve,reject){
		//el ajax normal
					var xhttp = new XMLHttpRequest();
					xhttp.open("POST",url,true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.onload = function(){
						//checar el status
						if(xhttp.status==200){
							//regresa el texto de respuesta
								resolve(xhttp.response);
							}else{
								//o regresa el texto status
								reject(Error(xhttp.statusText));
								}
					};
				//manejar errores de red				
					xhttp.onerror = function(){
						reject(Error(req.status.Text));
						};
				//hacer la peticion
					xhttp.send("dat="+datos);
			});
	}
	function cabezaped(nomcorto,idcte,seriefolio,fecha){
		const divped=document.getElementById("pedido");
		var reng0 = document.createElement("h1");
		var reng1= document.createElement("h2");
		var reng2= document.createElement("h2");
		var newlabel2 = document.createElement("Label");
		newlabel2.innerHTML = "fecha: ";
		var reng3= document.createElement("input");
		reng3.setAttribute("type","date");
		reng3.setAttribute("id","fechafact");
		reng3.setAttribute("value",fecha);
		newlabel2.setAttribute("for","fechafact");
		var reng4= document.createElement("select");
		var newlabel = document.createElement("Label");
		newlabel.innerHTML = "Seleccione la forma de Pago: ";
		reng0.innerHTML = "CLIENTE: " + nomcorto;
		reng1.innerHTML = "ID: "+idcte;
		reng2.innerHTML = "NO. FACTURA: "+seriefolio;
		reng4.setAttribute("id","fpago");
		newlabel.setAttribute("for","fpago");
		var option0 = document.createElement("option");
		option0.text = "EFECTIVO";
		option0.value=0;
		reng4.add(option0);
		var option1 = document.createElement("option");
		option1.text = "DEPOSITO";
		option1.value=2;
		reng4.add(option1);
		var option2 = document.createElement("option");
		option2.text = "CREDITO";
		option2.value=3;
		reng4.add(option2);
		divped.appendChild(reng0);
		divped.appendChild(reng1);
		divped.appendChild(reng2);
		divped.appendChild(newlabel2);
		divped.appendChild(reng3);
		divped.appendChild(newlabel);
		divped.appendChild(reng4);
		}

	function encabconcep(tblc){
		//los encabezados de la seccion conceptos
		var rengc= document.createElement('tr');
		var celdc=[];
		rengc.setAttribute("id", "re0");
		for(var i=0;i<8;i++){
			var texto;
			var cadena2="celda2"+i;
			celdc[i]=document.createElement('td');
			 celdc[i].setAttribute("id",cadena2);
			switch(i){
			case 0:
				texto="CANTIDAD";
				break;
			case 1:
				texto="UNIDAD";
				break;
			case 2:
				texto="CLAVE SAT";
				break;
			case 3:
				texto="NO. ID";
				break;
			case 4:
				texto="DESCRIPCION";
				break;
			case 5:
				texto="PRECIO U";
				break;
			case 6:
				texto="IMPUESTOS";
				break;
			case 7:
				texto="IMPORTE";
				break;
			break;
			default:
				texto="";
			}
			celdc[i].innerHTML=texto;
			rengc.appendChild(celdc[i]);
		}
			tblc.appendChild(rengc);
	}
	
	function listaconcep(conceptos,tbl2){
		var conclong=conceptos.length;
		for(var i=0;i<conclong;i++){
			hazconcep(tbl2,conceptos[i]);
			}
		}
	
	function hazconcep(tbl2,concepto){
		//crea un reglon de concepto
		const nreng = document.getElementsByTagName("tr").length;
		var rengt2= document.createElement('tr');
		rengt2.setAttribute("id", "re"+(nreng));
		//se revisa si el concepto trae impuesto
		if(concepto.children[0]){	
			var impuesto= concepto.children[0].children[0].children[0].getAttribute("Impuesto");
			var importeimp;
			if(impuesto==="002"){
				 importeimp = concepto.childNodes[0].childNodes[0].childNodes[0].getAttribute("Importe");	
			}else{importeimp="0"};
		}else{importeimp=""}

		//crear las celdas 
		for(var i=0;i<8;i++){
			var cadena3= 'celda'+(nreng+1)+i;
			var valor;
			cadena3= document.createElement('td');
			cadena3.setAttribute("id", cadena3);
			rengt2.appendChild(cadena3);
			switch(i){
			case 0:
				valor=concepto.getAttribute("Cantidad");
				break;
			case 1:
				valor=concepto.getAttribute("ClaveUnidad");
				break;
			case 2:
				valor=concepto.getAttribute("ClaveProdServ");
				break;
			case 3:
				valor=concepto.getAttribute("NoIdentificacion");
				break;
			case 4:
				valor=concepto.getAttribute("Descripcion");
				break;
			case 5:
				valor=concepto.getAttribute("ValorUnitario");
				break;
			case 6:
				valor= importeimp;
				break;
			case 7:
				valor=concepto.getAttribute("Importe");
				break;
			break;
			default:
				valor="";
			}
			cadena3.innerHTML=valor;
		}
		tbl2.appendChild(rengt2);
	}
		
	function cuerpoped(contenido){
		var conceptosex=contenido.conceptos;
		const tconceped = document.createElement("table");
		tconceped.setAttribute("id","tconceped");
		tconceped.setAttribute("class","cuerpob");
		const divped=document.getElementById("pedido");
		divped.appendChild(tconceped);
		const tconceped2=document.getElementById("tconceped");
		encabconcep(tconceped2);
		listaconcep(conceptosex,tconceped2);
		piefact(tconceped,contenido["stotal"],contenido["iva"],contenido["ieps"],contenido["total"])
		}
				   
		function piefact(tbl,subtotal,iva,ieps,total){
				//crea el pie
				for(var i=0; i<4;i++){
					var cadena= 'reng10'+i;
						cadena = document.createElement('tr');
						cadena.setAttribute("id", "re10"+i);
						for(var j=0;j<2;j++){
							var cadena2= 'celda10'+i+j;
							cadena2= document.createElement('td');
							cadena2.setAttribute("id", 'celd10'+i+j);
							cadena.appendChild(cadena2);
						}
						tbl.appendChild(cadena);
				}
				var subt=document.getElementById('celd1000');
				subt.innerHTML="SUBTOTAL";
				var subtv=document.getElementById('celd1001');
				subtv.innerHTML=subtotal;
				var subt=document.getElementById('celd1010');
				subt.innerHTML="IVA";
				var subtv=document.getElementById('celd1011');
				subtv.innerHTML=iva;
				var subt=document.getElementById('celd1020');
				subt.innerHTML="IEPS";
				var subtv=document.getElementById('celd1021');
				subtv.innerHTML=ieps;
				var subt=document.getElementById('celd1030');
				subt.innerHTML="TOTAL";
				var subtv=document.getElementById('celd1031');
				subtv.innerHTML=total;				
			}
		
		