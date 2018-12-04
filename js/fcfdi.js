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


function concepto(cant,uni,desc){
	//arma el concepto con unidad y cantidad
	var descom = cant+" "+uni+" "+desc;
	return descom;	
}

function desctraslados(nodoimp){
	var coltrans=nodoimp.getElementsByTagName("cfdi:Traslado");
	var longtrans=coltrans.length;
	var impiva=0;
	var impieps=0;
	var imps = new Array();
	for(var i=0; i<longtrans; i++){
		var impto = coltrans[i].attributes.getNamedItem("Impuesto").nodeValue
		if(impto=="002"){
			var ivacant=parseFloat(coltrans[i].attributes.getNamedItem("Importe").nodeValue);
			var impiva= impiva+ivacant;
			}
		if(impto=="003"){
			var iepscant=parseFloat(coltrans[i].attributes.getNamedItem("Importe").nodeValue);
			var impieps= impieps+iepscant;
			}
	}
	imps['iva'] = impiva;
	imps['ieps'] = impieps;
	return imps;
}
function descimp(coleccionimp){
	//recorre la coleccion impuestos
	var longcol= coleccionimp.length;
		for(var i= 0; i<longcol; i++){
			var tieneatrib=coleccionimp[i].attributes.length;
			if(tieneatrib>0){
				//es el nodo correcto
				var importe=desctraslados(coleccionimp[i]);
				return importe;
			}		
		}
}

function capiva(version,xdoc){
	var iva
	//buscar definiciones de iva
	var imp = xdoc.getElementsByTagName("cfdi:Impuestos");
	if(imp.length ==0){
		iva = 0;
	}else{
		iva =descimp(imp);		
		}
	return iva;
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


function leeXML(text,narch) {
	//lee el archivo cfdo xml y obtiene sus datos
	var xmlDoc;

	var cfdi = [];
	
	try{
		xmlDoc = $.parseXML(text);
		var comprob = xmlDoc.getElementsByTagName("cfdi:Comprobante")[0].attributes;
		var version = getver(comprob);
		var datosf = leeserief(version,comprob);
		var emisor = xmlDoc.getElementsByTagName("cfdi:Emisor")[0].attributes;
		var receptor = xmlDoc.getElementsByTagName("cfdi:Receptor")[0].attributes;
		var concepto =xmlDoc.getElementsByTagName("cfdi:Concepto")[0].attributes;
		var timbre=xmlDoc.getElementsByTagName("tfd:TimbreFiscalDigital")[0].attributes;
		var fecha;
		var fpago;
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
		var rfc
		var nombre
		var nombrea
		var rfcrecep
		var uuid;

		if (version == "3.3"){					 
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
			 imps = capiva(version,xmlDoc);
			 iva =imps['iva'];
			 ieps= imps['ieps'];
			 //si hay ieps, se modifica subtotal
			 if(ieps>0){stotal=stotal+ieps};
			 rfc = emisor.getNamedItem("Rfc").nodeValue;
			 nombrea=emisor.getNamedItem("Nombre");
			 if(nombrea){nombre = nombrea.nodeValue}else{nombre="SIN NOMBRE"};
			 rfcrecep =receptor.getNamedItem("Rfc").nodeValue;
			 concepa = concepto.getNamedItem("Descripcion");
			 concep = concepa.nodeValue
			 uuid = timbre.getNamedItem("UUID").nodeValue;
		}else {
			try{stotal = comprob.getNamedItem("subTotal").nodeValue;}catch(err){stotal= "ERROR STOTAL"}
			total = comprob.getNamedItem("total").nodeValue
			fecha= comprob.getNamedItem("fecha").nodeValue
			rfc = emisor.getNamedItem("rfc").nodeValue;
			 nombrea=emisor.getNamedItem("nombre")
			 
			if(nombrea){nombre = nombrea.nodeValue}else{nombre="SIN NOMBRE"};
			rfcrecep =receptor.getNamedItem("rfc").nodeValue;
			concepa = concepto.getNamedItem("descripcion");
			concep = concepa.nodeValue
		}

		cfdi={exito:0,
			  fecha: fecha,
			  fpago:fpago,
			  stotal:stotal,
			  iva:iva,
			  total:total,
			  seriefolio:seriefolio,
			  conceptoc:concep,
			  rfc:rfc,
			  nombre: nombre,
			  rfcrecep:rfcrecep,
			  uuid:uuid
			   };

	}catch(err){
		cfdi={exito:1,
			  fecha:	new Date(),
			  error: err
				};
	}
	return cfdi;	
};

