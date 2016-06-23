<?php

/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
if (is_object($mysqli)) {

	require_once('fpdf.php');
	
	class PDF extends FPDF
	{
		
	// Cabecera de página
	function Header()
	{
	    // Logo
	    $this->Image('../img/logocucina.jpg',10,10,60);
	    // Arial bold 18
	    $this->SetFont('Arial','B',18);
		//color relleno
		$this->SetFillColor(77,153,225);
		 // Salto de línea
	    $this->Ln(26);
		// Movernos a la derecha
	    $this->Cell(90);
	    // Título
	    $this->Cell(90,20,'Lista de Productos',1,1,'C',TRUE);
	    // Salto de línea
	    $this->Ln(20);
	}
	
	// Pie de página
	function Footer()
	{
	    // Posición: a 1,5 cm del final
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Número de página
	    $a_ = utf8_decode('á');
	    $this->Cell(0,10,'P'.$a_.'gina '.$this->PageNo().'/{nb}',0,0,'C');
	}
	
	// Tabla simple
function tablabas($header, $data)
{
    // Anchuras de las columnas
    $w = array(30,125,25);
    // Cabeceras
    $this->SetFillColor(95);
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();
    // Datos
    	//cambiar la fuente
	$this->SetFont('Arial','',14);
    foreach($data as $row)
    {
    	$this->Cell($w[0],6,$row['cod'],1);
        $this->Cell($w[1],6,$row['desc'],1);
        $this->Cell($w[2],6,$row['precio'],1,' ','R');
        $this->Ln(); 
    }
    // Línea de cierre
    $this->Cell(array_sum($w),0,'','T');
}
	
	}
	// Cargar los datos
	$data= $funcbase->leelprod($mysqli);
	// Títulos de las columnas
	$o_=utf8_decode('ó');
	$header = array('C'.$o_.'digo','Producto','Precio');
	// Creación del objeto de la clase heredada
	$pdf = new PDF();
	$pdf->AliasNbPages();
	$pdf->AddPage();
	//Tipografía Inicial
	$pdf->tablabas($header,$data);
	$pdf->Output();
}else {
        die ("<h1>'No se establecio la conexion a bd'</h1>");
    }
?>