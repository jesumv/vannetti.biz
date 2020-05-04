/**
 * funciones auxiliares para tratamiento de numeros
 * @author jmv
 */

function redondea(base,precis){
	//redondea una cifra a los decimales especificados
var m = Math.pow(10,precis)	;
var a = Math.round(base * m)/m;
return a;	
}