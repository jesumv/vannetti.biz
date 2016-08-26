<?php

/*** Autoload class files ***/ 
    function __autoload($class){
      require('../include/' . strtolower($class) . '.class.php');
    }
    
    $funcbase = new dbutils;
/*** conexion a bd ***/
    $mysqli = $funcbase->conecta();
	$nivel= $_GET["nivel"];
	$fecha= "FECHA DE ELABORACION: ".date("Y-m-d");
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
	    // Posición: a x  cm del final
	    $this->SetY(-40);
		$this->SetFont('Arial','I',10);
		$this->Cell(100,6,'ESTA LISTA ESTA SUJETA A CAMBIOS SIN PREVIO AVISO.',0,0,'L');
		$this->Cell(20,6,'FECHA DE ELABORACION: '.date('Y-m-d'),0,1,'L');
		$this->Cell(0,6,'Pedidos:777-313-1272; ventas@vannetti.biz',0,1,'C');
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
	$data= $funcbase->leelprod($mysqli,$nivel);
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