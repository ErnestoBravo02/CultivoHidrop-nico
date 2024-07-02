<?php
date_default_timezone_set('America/Bogota');

$servername = "localhost";
$username = "id22324935_administrador";
$password = "FCVTiot2024#";
$dbname = "id22324935_iotdata";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (!isset($_GET['sensor'])) {
    die("Tipo de sensor no especificado.");
}

// Obtener el tipo de sensor desde el parámetro de la URL
$sensorType = $_GET['sensor'];

// Validar el tipo de sensor
$validSensors = ['humidityDHT', 'temperatureDHT', 'temperatureDS18B20', 'pH'];
if (!in_array($sensorType, $validSensors)) {
    die("Tipo de sensor inválido.");
}

// Sanitizar el tipo de sensor
$sensorType = $conn->real_escape_string($sensorType);

// Consulta SQL para obtener los datos de los últimos 14 días
$sql = "SELECT DATE(timestamp) as date, ROUND(AVG($sensorType), 2) as avg_value 
        FROM SensorData 
        WHERE timestamp >= NOW() - INTERVAL 14 DAY 
        GROUP BY DATE(timestamp) 
        ORDER BY date";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Arreglo para almacenar los datos
$dates = [];
$values = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['date'];
        $values[] = $row['avg_value'];
    }
}

$conn->close();

// La petición AJAX se devuelve en formato JSON
if (isset($_GET['ajax'])) {
    $data = [
        'dates' => $dates,
        'values' => $values
    ];
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Datos de <?php echo htmlspecialchars($sensorType); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f4f6f9;
            color: #333;
            text-align: center;
        }
        .hr {
            height: 4px;
            min-width: 50%;
            max-width: 50%;
            background-color: #34495E;
            border: none;
            margin: 10px auto 30px auto;
        }
        .menu {
            background-color: #34495E;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }
        .menu img {
            height: 40px;
            margin: 8px 20px 8px 0;
        }
        .menu .menu-links {
            display: flex;
            flex-grow: 1;
            border-radius: 50px;
        }
        .menu a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            border-radius: 50px;
            transition: background-color 0.3s;
            transform: 0.3s;
        }
        .menu a.logout {
            margin-left: auto;
        }
        .menu a:hover {
            background-color: #5d6d7e;
            color: white;
            transform: scale(1.05);
            border-radius: 50px;
        }
        .content {
            margin: 20px;
            margin-top: 2%;
        }
        .chart-container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            color: white;
            background-color: #5D6D7E;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="menu">
        <img src="img/LOGOW-02.png" alt="Logo">
        <a href="dashboard.php" class="logout">Volver</a>
    </div>

    <div class="content">
        <h1>Histórico de Datos del Sensor</h1>
        <hr class="hr">
        <h2>Últimas dos Semanas</h2>
        <h5>id de sensor: <?php echo htmlspecialchars($sensorType); ?></h5>
    </div>

    <div class="chart-container">
        <canvas id="myChart"></canvas>
    </div>
    <table>
        <tr>
            <th>Fecha</th>
            <th>Valor Promedio</th>
        </tr>
        <?php foreach ($dates as $index => $date): ?>
            <tr>
                <td><?php echo htmlspecialchars($date); ?></td>
                <td><?php echo number_format($values[$index], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var dates = <?php echo json_encode($dates); ?>;
    var values = <?php echo json_encode($values); ?>.map(function(value) {
        return parseFloat(value).toFixed(2);
    });
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Valores Promedio Según su Fecha',
                data: values,
                borderColor: '#34495E',
                borderWidth: 4,
                pointBackgroundColor: '#34495E',
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return value.toFixed(2);
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>