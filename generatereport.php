<?php
date_default_timezone_set('America/Bogota');

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

//para poder generar el pdf necesitamos de fpdf
require('fpdf186/fpdf.php');

//periodo de fechas seleccionado
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$servername = "localhost";
$username = "id22324935_administrador";
$password = "FCVTiot2024#";
$dbname = "id22324935_iotdata";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta SQL para obtener el promedio de los datos de sensores para cada día en el rango de fechas seleccionado
$sql = "SELECT 
            DATE(timestamp) as date,
            AVG(humidityDHT) as avgHumidityDHT, 
            AVG(temperatureDHT) as avgTemperatureDHT, 
            AVG(temperatureDS18B20) as avgTemperatureDS18B20, 
            AVG(pH) as avgPH 
        FROM SensorData 
        WHERE DATE(timestamp) BETWEEN '$start_date' AND '$end_date'
        GROUP BY DATE(timestamp)";
$result = $conn->query($sql);

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('img/LOGOBLUE.png',15,7,25);
        $this->SetFont('Arial','B',18);
        $this->Ln(1);
        $this->Cell(0,10,'Monitoreo de Cultivo Hidroponico',0,1,'C');
        $this->Cell(0,10,'Reporte de Sensores',0,1,'C');
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
    }

    function ReportTable($header, $data)
    {
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(93, 109, 126);
        $this->SetTextColor(255);
        $this->SetDrawColor(50,50,100);
        
        // Cabecera
        foreach($header as $col)
            $this->Cell(38,7,$col,1,0,'C',true);
        $this->Ln();
        
        $this->SetFillColor(200,220,255);
        $this->SetTextColor(0);
        
        // Datos
        $this->SetFont('Arial','',12);
        foreach($data as $row)
        {
            foreach($row as $col)
                $this->Cell(38,6,$col,1);
            $this->Ln();
        }
    }
    
    function AddParagraph()
    {
        $this->SetFont('Arial','',12);
        $this->Ln(12);
        $this->MultiCell(0,10,'Este PDF se ha generado a partir de los datos recogidos por los sensores de Temperatura, pH y Humedad durante el periodo escogido. El documento presenta un promedio de los datos registrados por cada sensor cada dia del periodo seleccionado.',0,'J');
        $this->Ln(5);
    }
    
    function SecondTitle()
    {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'Tabla',0,1,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->AddParagraph();
$pdf->SecondTitle();

if ($result->num_rows > 0) {
    $header = array('Fecha', 'Temp. Agua', 'Nivel pH', 'Temp. Ambiente', 'Hum. Ambiente');
    $data = array();
    while($row = $result->fetch_assoc()) {
        $data[] = array($row['date'], 
        number_format($row['avgTemperatureDHT'], 2). ' C', 
        number_format($row['avgPH'], 2), 
        number_format($row['avgTemperatureDS18B20'], 2). ' C', 
        number_format($row['avgHumidityDHT'], 2). ' %');
    }
    $pdf->ReportTable($header, $data);
} else {
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0,10,'No se encontraron datos para el rango de fechas seleccionado.',0,1);
}

$pdf->Output('D', 'reporte_sensores.pdf');

$conn->close();
?>