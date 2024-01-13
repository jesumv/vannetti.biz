<?php

/*** Autoload class files ***/ 
function myAutoload($ClassName)
{
    require('../include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

    
	$pedido= $_GET["pedido"];
	
	function leecliente($mysqli,$pedido){
		 $sqlCommand = "SELECT t2.nom_corto,t1.fecha  FROM pedidos AS t1 LEFT JOIN clientes AS t2 ON t1.idclientes = t2.idclientes
 				WHERE t1.idpedidos =".$pedido." ;";
		// Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE PEDIDO. ".mysqli_error($mysqli));
			 //inicializacion de arreglo
				while($tempo=mysqli_fetch_array($query1)){
			 		$result[] = array('fecha' => $tempo['fecha'],'nombre' => $tempo['nom_corto']);
			 };
            /* liberar la serie de resultados */
                  mysqli_free_result($query1);
                  /* cerrar la conexion */
                  mysqli_close($mysqli);
            if($result){
              return $result;  
            }
            else {
                 die('no hay resultados para el pedido');
            }			
	}
	
	
	function leeapedido($mysqli,$pedido){
			 $sqlCommand = "SELECT t1.cant,t2.nombre,t1.preciou, t1.preciot FROM artsped AS t1 LEFT JOIN productos AS t2 ON t1.idproductos = t2.idproductos
 				WHERE t1.idpedido =".$pedido." ;";		
	 // Execute the query here now
			 $query1=mysqli_query($mysqli, $sqlCommand) or die ("ERROR EN CONSULTA DE ARTICULOS. ".mysqli_error($mysqli));
			 //inicializacion de arreglo
				while($tempo=mysqli_fetch_array($query1)){
			 		$result[] = array('cant' => $tempo['cant'],'nombre' => $tempo['nombre'],'preciou' => $tempo['preciou']
					,'preciot' => $tempo['preciot']);
			 };
            /* liberar la serie de resultados */
                  mysqli_free_result($query1);
                  /* cerrar la conexion */
                  mysqli_close($mysqli);
            if($result){
              return $result;  
            }
            else {
                 die('no hay resultados para el pedido');
            }
		  	
		  }	
	
	$funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
if (is_object($mysqli)) {
	$datos = leecliente($mysqli,$pedido);
	require_once('fpdf.php');
	
	class PDF extends FPDF
	{
			
	// Cabecera de página
	function Header()
	{
	    // Logo
	    $this->Image('../img/logocucina.jpg',5,5,20);
	    // Arial bold 10
	    $this->SetFont('Arial','B',10);
		//color relleno
		$this->SetFillColor(95,190,90);
		 // Salto de línea
	    $this->Ln(-4);
		// Movernos a la derecha
	    $this->Cell(25);
	    // Título
	    $this->Cell(85,20,'NOTA DE REMISION',1,1,'C',TRUE);
	    // Salto de línea
	    $this->Ln(10);
	//Tipografía Inicial
		$this->SetFont('Arial','',8);
		$pedido= $_GET["pedido"];
		$this->cabeza($pedido);
	}
	
	// Pie de página
	function Footer()
	{
	    // Posición: a x  cm del final
	    $this->SetY(-45);
		$this->SetFont('Arial','I',10);
		$this->Cell(100,6,'RECIBI.',0,0,'L');
		$this->Cell(0,6,'Pedidos:55-5966-4338; ventas@vannetti.biz',0,1,'C');
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Número de página
	    $a_ = utf8_decode('á');
	    $this->Cell(0,10,'P'.$a_.'gina '.$this->PageNo().'/{nb}',0,0,'C');
	}
	
	//tabla de encabezado
	function cabeza($pedido){
		$fecha= $datos['fecha'];
		$cliente= $datos['nombre'];
		$this->SetFillColor(95);
		$cliente='PRUEBA';
		$dia=$fecha;
		$mes=date('m');
		$year=date('Y');
		$this->Cell(50,7,'REMISION NO. ',1,0,'L',true);
		$this->Cell(20,7,'DIA',1,0,'C',true);
		$this->Cell(20,7,'MES',1,0,'C',true);
		$n_=utf8_decode('Ñ');
		$this->Cell(20,7,'A'.$n_.'O',1,1,'C',true);
		$this->Cell(50,7,$pedido,1,0,'C');
		$this->Cell(20,7,$dia,1,0,'C');
		$this->Cell(20,7,$mes,1,0,'C');
		$this->Cell(20,7,$year,1,1,'C');
		$this->Cell(110,7,'CLIENTE: '.$cliente,1,0,'L');
		$this->ln();
	}
	
	// Tabla simple
function tablabas($header, $data)
{
    // Anchuras de las columnas
    $w = array(10,70,15,15);
    // Cabeceras
    $this->SetFillColor(95);
    for($i=0;$i<count($header);$i++){
    	$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
	}
        
    $this->Ln();
    // Datos
    	//suma de cantidades
    	$total= 0;
    foreach($data as $row)
    {
    	$total = number_format($total + $row['preciot'],2,'.','');	
    	$this->Cell($w[0],6,$row['cant'],1);
        $this->Cell($w[1],6,$row['nombre'],1);
		$numerof=number_format($row['preciou'], 2, '.', '');
        $this->Cell($w[2],6,$numerof,1,0,'R');
		$preciotf=number_format($row['preciot'], 2, '.', '');
        $this->Cell($w[2],6,$preciotf,1,1,'R'); 
    }
	
	//totales
		$this->Cell(80,6,'',1,0);
		$this->Cell($w[2],6,'TOTAL',1,0,'R',true);
		$this->Cell($w[2],6,$total,1,1,'R');
    // Línea de cierre
    //$this->Cell(array_sum($w),0,'','T');
}
	
	}
	// Cargar los datos
	$data= leeapedido($mysqli,$pedido);
	// Títulos de las columnas
	$header = array('CANT.','CONCEPTO','PRECIO','IMPORTE');
	// Creación del objeto de la clase heredada
	$pdf = new PDF();
	$pdf->AliasNbPages();
	$pdf->SetLeftMargin(5);
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(2,50);
	$pdf->ln();
	$pdf->tablabas($header,$data);
	$pdf->Output();
}else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
?>