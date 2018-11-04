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

function capiva(version,xdoc){
	var iva
	//buscar definiciones de iva
	var imp = xdoc.getElementsByTagName("cfdi:Impuestos");
	var totimptr =xdoc.getElementsByTagName("totalImpuestosTrasladados");
	if(imp.length ==0){
		iva = 0;
	}else{
		var novacio = imp[0].hasChildNodes();
		if(novacio){
			//sí hay datos en el nodo impuestos
			var totalimp = imp[0].attributes;
			if(totalimp.length!=0){
				//si existe el atributo de total impuestos
				iva = totalimp.getNamedItem("totalImpuestosTrasladados").nodeValue;
			}else{
				
				//si no hay total, se busca por nodo
				//definicion de etiqueta a buscar
				var etiq;
				var etiq2;
				var nomimp;
				var ntasa;
				if(version=="3.2"){
					etiq= "impuesto";
					etiq2="importe";
					nomimp="IVA";
					ntasa ="tasa";
					}else{
						etiq= "Impuesto";
						etiq2="Importe";
						nomimp = "002"
						ntasa = "TasaOCuota"
						}
				var traslado = xdoc.getElementsByTagName("cfdi:Traslado");
				for (var i=0; traslado.length; i++){
					var atribs = traslado[i].attributes;
					var nomtem = atribs.getNamedItem(etiq).nodeValue
					if(nomtem == nomimp){
						var haytasa = atribs.getNamedItem(ntasa);
						if(haytasa){
							var tasa = atribs.getNamedItem(ntasa).nodeValue
							if(tasa.includes("16")){iva = atribs.getNamedItem(etiq2).nodeValue; break;}else{iva="0"; break;}
							
						}else{
							iva="0"; break;
						}
							
						
						}
				}
			}
			
		}else{
			//si no hay datos
			iva = ""
		}
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
		var fecha;
		var fpago;
		var seriefolio = datosf["folio"]+datosf["serie"];
		var iva 
		var concepa ;
		var concepa1;
		var concep;
		var stotal;
		var total;
		var rfc
		var nombre
		var nombrea
		var rfcrecep

		if (version == "3.3"){					 
			 var haydescu = comprob.getNamedItem("Descuento");
			 //si hay descuento se modifica subtotal
			 if(haydescu){
				 var descu = comprob.getNamedItem("Descuento").nodeValue;
				 stotal = comprob.getNamedItem("SubTotal").nodeValue - descu;
			 }else{
				 stotal = comprob.getNamedItem("SubTotal").nodeValue	 
			 };
			 total = comprob.getNamedItem("Total").nodeValue
			 fecha= comprob.getNamedItem("Fecha").nodeValue
			 fpago= comprob.getNamedItem("FormaPago").nodeValue;
			 iva = capiva(version,xmlDoc);
			 rfc = emisor.getNamedItem("Rfc").nodeValue;
			 nombrea=emisor.getNamedItem("Nombre");
			 if(nombrea){nombre = nombrea.nodeValue}else{nombre="SIN NOMBRE"};
			 rfcrecep =receptor.getNamedItem("Rfc").nodeValue;
			 concepa = concepto.getNamedItem("Descripcion");
			 concep = concepa.nodeValue
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

