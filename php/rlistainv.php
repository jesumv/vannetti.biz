<?php

/*** Autoload class files ***/ 
function myAutoload($ClassName)
{
    require('include/' . strtolower($ClassName) . '.class.php');
}

spl_autoload_register('myAutoload');

    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
	$cat= $_GET["cat"];
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
	    $this->Cell(90,20,'Lista de Inventarios',1,1,'C',TRUE);
	    // Salto de línea
		$this->Ln(12);
		$this->SetFont('Arial','B',12);
		$this->SetX(100);
	    $this->Cell(60,5,'Fecha:____/______/____',0,1,'L',false);
	    $this->Ln(6);
	}
	
	// Pie de página
	function Footer()
	{
	    // Posición: a x  cm del final
	    $this->SetY(-25);
		$this->SetFont('Arial','I',10);
		$this->Cell(0,4,'© Vannetti Cucina',0,1,'C');
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
	$this->SetFont('Arial','',12);
    foreach($data as $row)
    {
    	$this->Cell($w[0],6,$row['cod'],1);
        $this->Cell($w[1],6,$row['desc'],1);
        $this->Cell($w[2],6,"",1,' ','R');
        $this->Ln(); 
    }
	//renglones extras
	for($i=0;$i<10;$i++)
    {
    	$this->Cell($w[0],6,"",1);
        $this->Cell($w[1],6,"",1);
        $this->Cell($w[2],6,"",1);
        $this->Ln(); 
    }
    // Línea de cierre
    $this->Cell(array_sum($w),0,'','T');
}
	
	}
	// Cargar los datos
	$data= $funcbase->leelinv($mysqli,$cat);
	// Títulos de las columnas
	$o_=utf8_decode('ó');
	$header = array('C'.$o_.'digo','Producto','Exist.');
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