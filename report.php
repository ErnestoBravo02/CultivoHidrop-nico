<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Reporte - Dashboard IoT</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f4f6f9;
            color: #333;
            text-align: center;
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
        .contenttitle {
            margin: 20px;
            margin-top: 2%;
        }
        .content {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .content h1 {
            margin: 40px 0 10px 0;
            font-size: 24px;
            color: #34495E;
        }
        .hr {
            height: 4px;
            min-width: 50%;
            max-width: 50%;
            background-color: #34495E;
            border: none;
            margin: 10px auto 30px auto;
        }
        .form-container {
            max-width: 325px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .form-container label {
            margin-top: 10px;
            font-weight: bold;
        }
        .form-container input[type="date"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-container input[type="submit"] {
            background-color: #34495E;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container input[type="submit"]:hover {
            background-color: #5d6d7e;
        }
    </style>
</head>
<body>
<div class="menu">
        <img src="img/LOGOW-02.png" alt="Logo">
        <div class="menu-links">
            <a href="dashboard.php">Visualización</a>
            <a href="report.php">Reporte</a>
        </div>
        <a href="logout.php" class="logout"><abbr title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i></abbr></a>
    </div>
    
    <div class="contenttitle">
        <h1>Generar Reporte</h1>
        <hr class="hr">
        <h2>Seleccionar periodo</h2>
    </div>
    
    <div class="form-container">
        <form action="generatereport.php" method="POST">
            <label for="start_date">Fecha inicial:</label>
            <input type="date" id="start_date" name="start_date" required>
            <label for="end_date">Fecha final:</label>
            <input type="date" id="end_date" name="end_date" required>
            <p></p>
            <input type="submit" value="Generar Reporte">
        </form>
    </div>
</body>
</html>