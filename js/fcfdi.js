/**
 * esta pagina incluye las funciones para la lectura de cfdis
 */

			
			
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

function getconcep(conceptos){
	//cuenta los conceptos de la factura
	var noconcep = conceptos.length;
	return noconcep;
}

function extraeiva(traslados2){
	//obtiene el valor de iva y otros
	var longi= traslados2.length;
	var monto= 0;
	var otros = false;
	var impto;
	for(var i=0; i<longi;i++){
		impto = traslados2[i].attributes["Impuesto"].nodeValue;
		if(impto == "002"){monto = traslados2[i].attributes["Importe"].nodeValue;}
		if(impto == "003"){otros = true;}
	}
	return {monto:monto,
			otros:otros};
}

function tieneimp(cfdiimp,tiponod){
	//revisa si hay nodo de impuestos y regresa indicador
	var niv1 = cfdiimp.childNodes;
	var cfdiarr = [].slice.call(niv1);
	var tiene1 = cfdiarr.some(function(element,index,array){return element.nodeName == "cfdi:Traslados"});
	var tienet;
		if(tiene1){
			//si tiene nodo cfdi:traslados
			var niv2 = niv1[tiponod].childNodes;
				if(niv2){
					//si tiene nodo cfdi:traslado
						var niv3 = niv2[tiponod].attributes;
					if(niv3){
						//si tiene atributo impuestos
						var iva = niv3.getNamedItem("Impuesto").nodeValue;
							if(iva ="002"){tienet = true}else{tienet= false}
					}	
				}else{tienet = false}
			
		}else{tienet = false}			
		
	return tienet;
}
function concepto(cant,uni,desc){
	//arma el concepto con unidad y cantidad
	var descom = cant+" "+uni+" "+desc;
	return descom;	
}


function leeXML(text,narch) {
	//lee el archivo cfdo xml y obtiene sus datos
	var xmlDoc;

	var cfdi = [];
	
	try{
		xmlDoc = $.parseXML(text);
		//cfdi:Comprobante
		var niv1 = xmlDoc.documentElement.childNodes;
		//cfdi:Impuestos depende del tipo de archivo
		var impini;
		var intras11;
		if(niv1[0].nodeType==1){
			impini = niv1[3];
			intras11 = 0;
		}else{
			if(niv1[3].nodeType==1){impini = niv1[4]; intras11 = 0}else{impini = niv1[7]; intras11 = 1}
			}
		var tras1 = impini.childNodes;
		var comprob = xmlDoc.getElementsByTagName("cfdi:Comprobante")[0].attributes;
		var fecha= comprob.getNamedItem("Fecha").nodeValue;
		var version = getver(comprob);
		var serie;
		var folio;
		var seriee = comprob.getNamedItem("Serie");
		var serie;
		var seriefolio = "";
		if(seriee){
			serie= comprob.getNamedItem("Serie").nodeValue;
			seriefolio= seriefolio + serie +" "
			}
		var folioe = comprob.getNamedItem("Folio");
		if(folioe){
			folio = comprob.getNamedItem("Folio").nodeValue;
			seriefolio= seriefolio + folio}
		var tienei = tieneimp(impini,intras11);
		var emisor = xmlDoc.getElementsByTagName("cfdi:Emisor")[0].attributes;
		var receptor = xmlDoc.getElementsByTagName("cfdi:Receptor")[0].attributes;
		var conceptos =xmlDoc.getElementsByTagName("cfdi:Concepto");
		var conco = conceptos[0].attributes;
		var canti = conco.getNamedItem("Cantidad").nodeValue;
		var uni = conco.getNamedItem("ClaveUnidad").nodeValue;
		var desci = conco.getNamedItem("Descripcion").nodeValue;
		var conceptoc = concepto(canti,uni,desci);
		var traslados;
		var iva ;
		var otrosi;
		var stotal;
		var descu;
		var total;
		var rfc;
		var nombre;
		var nombrea;
		var rfcrecep;
		//segun version
		if (version == "3.3"){					 
			 var haydescu = comprob.getNamedItem("Descuento");
			 if(haydescu){
				 descu = comprob.getNamedItem("Descuento").nodeValue;
			 }else{
				 descu = null;
				
			 };
			 //definir impuestos si los hay
			 if(tienei){
				var tras11=tras1[intras11].getElementsByTagName("cfdi:Traslado");
				 imptos = extraeiva(tras11);
				 otrosi = imptos.otros;
				 iva = imptos.monto;
			 }else{iva = 0;
			 otrosi = 0;
			 }
			 
			 total = comprob.getNamedItem("Total").nodeValue
			 if(otrosi==true){stotal = Number(total)-Number(iva);}else if (haydescu){
				 stotal = parseFloat(Number(comprob.getNamedItem("SubTotal").nodeValue)-Number(descu)).toFixed(2)}else{
				 stotal = comprob.getNamedItem("SubTotal").nodeValue;
				 }
			 rfc = emisor.getNamedItem("Rfc").nodeValue;
			 nombrea=emisor.getNamedItem("Nombre");
			 if(nombrea){nombre = nombrea.nodeValue}else{nombre="SIN NOMBRE"};
			 rfcrecep =receptor.getNamedItem("Rfc").nodeValue;
			//otras versiones
		}else {
			//versiones anteriores a la 3.3 -completar
			try{stotal = comprob.getNamedItem("subTotal").nodeValue;}catch(err){stotal= "ERROR STOTAL"}
			total = comprob.getNamedItem("total").nodeValue
			fecha= comprob.getNamedItem("fecha").nodeValue
			rfc = emisor.getNamedItem("rfc").nodeValue;
			 nombrea=emisor.getNamedItem("nombre")
			 
			 if(nombrea){nombre = nombrea.nodeValue}else{nombre="SIN NOMBRE"};
			rfcrecep =receptor.getNamedItem("rfc").nodeValue;
		}
		cfdi={exito:0,
			  fecha: fecha,
			  stotal:stotal,
			  iva:iva,
			  otrosi:otrosi,
			  total:total,
			  seriefolio:seriefolio,
			  conceptoc:conceptoc,
			  rfc:rfc,
			  nombre: nombre,
			  rfcrecep:rfcrecep
			   };

	}catch(err){
		cfdi={exito:1,
			  fecha:	new Date(),
			  error: err
				};


	}
	return cfdi;	
};

