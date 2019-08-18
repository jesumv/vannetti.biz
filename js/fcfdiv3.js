/**
 * esta pagina incluye las funciones para la lectura de cfdis
 */

function checarecep(rfc){
				var resul;
				//revisa que la factura sea para receptor correcto
				var recepo = "MAVJ621021AQA";
				if(rfc == recepo){
					resul = 0;
				}else{
					resul= -1;
				}
				return resul;
			}			
			
function getver(xmlDoc){
	//obtiene la version del cfdi
				var versiona= xmlDoc.getNamedItem("Version")
				var version
				if(!versiona)
				{
					version = xmlDoc.getNamedItem("version").nodeValue
				}else{
					 version = xmlDoc.getNamedItem("Version").nodeValue;
				}
				
				return version
			}


function concepto(cant,uni,desc){
	//arma el concepto con unidad y cantidad
	var descom = cant+" "+uni+" "+desc;
	return descom;	
}

function desctraslados(concepto){
	//extrae traslados del nodo concepto
	var traslados=concepto.getElementsByTagName("cfdi:Traslado");
	var longtrans=traslados.length;
	var impiva=0;
	var impieps=0;
	var imps = [];
	for(var i=0; i<longtrans; i++){
		var impto = traslados[i].attributes.getNamedItem("Impuesto").nodeValue
		var tipoimpto=traslados[i].attributes.getNamedItem("TipoFactor").nodeValue
		//si esta exento del impuesto no hay importe
		if(tipoimpto==!"Exento"){
			switch(impto) {
			  case '002':
				var ivacant=parseFloat(traslados[i].attributes.getNamedItem("Importe").nodeValue);
				 impiva= impiva+ivacant;
			    break;
			  case '003':
				var iepscant=parseFloat(traslado[i].attributes.getNamedItem("Importe").nodeValue);
				impieps= impieps+iepscant;
			    break;
			  default:
			    impiva=0;
			  	impieps=0;
			}
		}else{
			impiva="";
			imipes="";
		}

	}
	imps['iva'] = impiva;
	imps['ieps'] = impieps;
	return imps;
}
function descimp(colecconcep){
	//recorre la coleccion conceptos
	var longcol= colecconcep.length;
	var importe=[];
	importe['iva']=0;
	importe['ieps']=0;
	
		for(var i= 0; i<longcol; i++){
				 var importesact=desctraslados(colecconcep[i]);
				 	importe['iva']=importe['iva']+ importesact['iva'];
					importe['ieps']=importe['ieps']+ importesact['ieps']; 
		}
		return importe;
}


function calcimptos(nodoimpuestos){
	//extrae los datos del nodo cfdi:impuestos global
	var imptos =[];
	imptos['iva']=0;
	imptos['ieps']=0;
	
	//buscar definiciones de iva
	var traslados = nodoimpuestos.getElementsByTagName("cfdi:Traslados");
	var trasladocol=traslados[0].children;
		//se sumaran los valores de impuesto por cada traslado
		var longtras=trasladocol.length;
		for(var i=0;i<longtras;i++){
			var traslado=trasladocol[i].attributes
			//examina el tipo de impuesto
			var tipo=traslado.getNamedItem("Impuesto").nodeValue;
			var importe=parseFloat(traslado.getNamedItem("Importe").nodeValue);
			switch(tipo){
			case "002":
				imptos['iva']=imptos['iva']+importe;
			break;
			case "003":
				imptos['ieps']=imptos['ieps']+importe;
			break;	
			default:
				imptos['iva']="";
				imptos['ieps']="";
			}
		}	
	return imptos;
	}



function leeserief(version,comprob){
	//lee serie y folio dependiendo de version, si los tiene
	var serie;
	var folio32 = comprob.getNamedItem("folio");
	var serie32= comprob.getNamedItem("serie");
	var folio33 = comprob.getNamedItem("Folio");
	var serie33= comprob.getNamedItem("Serie");
	var resul =[];
	if (version == "3.3"){
		if(serie33){resul["serie"] = comprob.getNamedItem("Serie").nodeValue}else{resul["serie"] = ""};
		if(folio33){resul["folio"] = comprob.getNamedItem("Folio").nodeValue}else{resul["folio"] = ""};
	}else{
		if(serie32){resul["serie"] = comprob.getNamedItem("serie").nodeValue}else{resul["serie"] = ""};
		if(folio32){resul["folio"] = comprob.getNamedItem("folio").nodeValue}else{resul["folio"] = ""};
	}
	return resul;
}

function leeXMLing(texto){
	//lee el archivo cfdi de ingresos xml y obtiene sus datos
	var xmlDoc;
	var cfdi = [];
	try{
		var parser = new DOMParser();
		var xmlDoc = parser.parseFromString(texto,"text/xml");
		var comprob = xmlDoc.getElementsByTagName("cfdi:Comprobante")[0];
		var atribcomp = comprob.attributes;
		var nodos= comprob.children;
		var tipoc=atribcomp.getNamedItem("TipoDeComprobante").nodeValue;
		//Si el cfdi no es de ingreso se genera excepcion
		if(tipoc!=="I"){throw new Error('EL CFDI NO ES DE INGRESO')};
		var version = getver(atribcomp);
		var datosf = leeserief(version,atribcomp);
		var emisor = xmlDoc.getElementsByTagName("cfdi:Emisor")[0].attributes;
		var receptor = xmlDoc.getElementsByTagName("cfdi:Receptor")[0].attributes;
		var conceptos=[];
		var conceptos = xmlDoc.getElementsByTagName("cfdi:Concepto");
		var concepto =xmlDoc.getElementsByTagName("cfdi:Concepto")[0].attributes;
		var timbre=xmlDoc.getElementsByTagName("tfd:TimbreFiscalDigital")[0].attributes;
		var fecha;
		var fpago;
		var metpago;
		var serie = datosf["serie"];
		var folio = datosf["folio"];
		var seriefolio = datosf["folio"]+datosf["serie"];
		var iva;
		var ieps;
		var concepa ;
		var concepa1;
		var concep;
		var stotal;
		var total;
		var rfc;
		var nombre;
		var nombrea;
		var rfcrecep;
		var nombrer;
		var nombrerecep;
		var uuid;					 
		var haydescu = atribcomp.getNamedItem("Descuento");
		var astotal= atribcomp.getNamedItem("SubTotal").nodeValue;
		
			 //si hay descuento se modifica subtotal
			 if(haydescu){
				 var descu = atribcomp.getNamedItem("Descuento").nodeValue;
				 stotal = parseFloat(astotal) - parseFloat(descu);
			 }else{
				 stotal = parseFloat(astotal);	 
			 };
			 total = atribcomp.getNamedItem("Total").nodeValue
			 fecha= atribcomp.getNamedItem("Fecha").nodeValue
			 if(comprob.hasAttribute("FormaPago")){fpago= atribcomp.getNamedItem("FormaPago").nodeValue;}else{
				 fpago="";
			 }
			 metpago= atribcomp.getNamedItem("MetodoPago").nodeValue;
			 //definicion de impuestos
			 //Verificar si hay nodo impuestos
			 	if(nodos[3].localName==="Impuestos"){
			 		var impsatrib=nodos[3].attributes;
					 var totimps=impsatrib.getNamedItem("TotalImpuestosTrasladados").nodeValue;
					 var imps = calcimptos(nodos[3]);
					 iva =imps['iva'];
					 ieps= imps['ieps']; 
			 	}else{
			 		iva="";
			 		ieps="";
			 	}
			 //si hay ieps, se modifica subtotal
			 	var stotal= ieps > 0 ? parseFloat(stotal)+parseFloat(ieps) : parseFloat(stotal);
			 //if(ieps>0){stotal=parseFloat(stotal)+parseFloat(ieps)};
			 rfc = emisor.getNamedItem("Rfc").nodeValue;
			 nombrea=emisor.getNamedItem("Nombre");
			 //si no hay nombre, se agrega generico
			 if(nombrea){nombre = nombrea.nodeValue}else{nombre="SIN NOMBRE"};
			 rfcrecep =receptor.getNamedItem("Rfc").nodeValue;
			 nombrer = receptor.getNamedItem("Nombre");
			 if(nombrer){nombrerecep = nombrer.nodeValue}else{nombrerecep="SIN NOMBRE"};
			 concepa = concepto.getNamedItem("Descripcion");
			 concep = concepa.nodeValue
			 uuid = timbre.getNamedItem("UUID").nodeValue;	
				cfdi={exito:0,
						  fecha: fecha,
						  fpago:fpago,
						  metpago:metpago,
						  tipoc:tipoc,
						  stotal:stotal,
						  iva:iva,
						  ieps:ieps,
						  total:total,
						  serie:serie,
						  folio:folio,
						  seriefolio:seriefolio,
						  conceptos:conceptos,
						  conceptoc:concep,
						  rfc:rfc,
						  nombre: nombre,
						  rfcrecep:rfcrecep,
						  nombrerecep:nombrerecep,
						  uuid:uuid
				}
	}
	catch(err){
		cfdi={exito:1,
				  fecha:	new Date(),
				  error: err
					};
		}
		return cfdi;
	}

function leeXML(text) {
	//lee el archivo cfdo xml y obtiene sus datos
	var xmlDoc;
	var cfdi = [];
	try{
		xmlDoc = $.parseXML(text);
		var comprob = xmlDoc.getElementsByTagName("cfdi:Comprobante")[0].attributes;
		var tipoc=comprob.getNamedItem("TipoDeComprobante").nodeValue;
		//solo se procesa si el tipo de comprobante es ingreso
		if(tipoc=="I"){
			var version = getver(comprob);
			var datosf = leeserief(version,comprob);
			var emisor = xmlDoc.getElementsByTagName("cfdi:Emisor")[0].attributes;
			var receptor = xmlDoc.getElementsByTagName("cfdi:Receptor")[0].attributes;
			var conceptos=[];
			var conceptos = xmlDoc.getElementsByTagName("cfdi:Concepto");
			var concepto =xmlDoc.getElementsByTagName("cfdi:Concepto")[0].attributes;
			var timbre=xmlDoc.getElementsByTagName("tfd:TimbreFiscalDigital")[0].attributes;
			var fecha;
			var fpago;
			var metpago;
			var serie = datosf["serie"];
			var folio = datosf["folio"];
			var seriefolio = datosf["folio"]+datosf["serie"];
			var imps;
			var iva;
			var ieps;
			var concepa ;
			var concepa1;
			var concep;
			var astotal;
			var stotal;
			var total;
			var rfc;
			var nombre;
			var nombrea;
			var rfcrecep;
			var nombrerecep;
			var uuid;				 
			var haydescu = comprob.getNamedItem("Descuento");
				 //si hay descuento se modifica subtotal
				 if(haydescu){
					 var descu = parseFloat(comprob.getNamedItem("Descuento").nodeValue);
					 var astotal= parseFloat(comprob.getNamedItem("SubTotal").nodeValue);
					 stotal = astotal - descu;
				 }else{
					 stotal = parseFloat(comprob.getNamedItem("SubTotal").nodeValue);	 
				 };
				 total = comprob.getNamedItem("Total").nodeValue
				 fecha= comprob.getNamedItem("Fecha").nodeValue
				 fpago= comprob.getNamedItem("FormaPago").nodeValue;
				 metpago= comprob.getNamedItem("MetodoPago").nodeValue;
				 imps = capiva(version,xmlDoc);
				 iva =imps['iva'];
				 ieps= imps['ieps'];
				 //si hay ieps, se modifica subtotal
				 if(ieps>0){stotal=stotal+ieps};
				 rfc = emisor.getNamedItem("Rfc").nodeValue;
				 nombrea=emisor.getNamedItem("Nombre");
				 if(nombrea){nombre = nombrea.nodeValue}else{nombre="SIN NOMBRE"};
				 rfcrecep =receptor.getNamedItem("Rfc").nodeValue;
				 nombrerecep =receptor.getNamedItem("Nombre").nodeValue;
				 concepa = concepto.getNamedItem("Descripcion");
				 concep = concepa.nodeValue
				 uuid = timbre.getNamedItem("UUID").nodeValue;	
					cfdi={exito:0,
							  fecha: fecha,
							  fpago:fpago,
							  metpago:metpago,
							  tipoc:tipoc,
							  stotal:stotal,
							  iva:iva,
							  total:total,
							  serie:serie,
							  folio:folio,
							  seriefolio:seriefolio,
							  conceptos:conceptos,
							  conceptoc:concep,
							  rfc:rfc,
							  nombre:nombre,
							  rfcrecep:rfcrecep,
							  nombrerecep:nombrerecep,
							  uuid:uuid
							   };
			
		}else{
			cfdi={exito:0,
			tipoc:tipoc,		
			}
		}





	}catch(err){
		cfdi={exito:1,
			  fecha:	new Date(),
			  error: err
				};
	}
	return cfdi;	
};

