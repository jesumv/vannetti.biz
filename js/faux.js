/**
 * @author jmv
 * este script contiene las funciones comunes a varios scripts
 */



function addnodo(elem,clase){
	if (clase === undefined) clase = "";
	var para = document.createElement(elem);
	var para2 = document.createElement(elem);
	para.className = "ui-block-a";
	para.className = "ui-bar ui-bar-a";
	var node = document.createTextNode("prueba");
	para.appendChild(para2);
	para2.appendChild(node);
	var element = document.getElementById("octabla");
	element.appendChild(para);
};
