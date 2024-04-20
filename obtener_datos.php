<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registro y visualización de temperatura</title>
    <meta http-equiv="refresh" content="15"> <!-- Actualizar la página cada 10 segundos -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('temperatura2.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            animation: fadeIn 1s ease; /* Animación de desvanecimiento */
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        h1, h2 {
            color: #007bff;
            text-align: center;
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #007bff;
            border-radius: 3px;
            box-sizing: border-box;
            transition: background-color 0.3s ease;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #4CAF50;
            animation: highlight 0.5s ease;
        }

        @keyframes highlight {
            0% {
                background-color: rgba(76, 175, 80, 0.3);
            }
            100% {
                background-color: transparent;
            }
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        td {
            background-color: #f2f2f2;
            color: #333;
        }

        p {
            color: #666;
            text-align: center;
        }

        .error {
            color: #ff0000;
            text-align: center;
        }

        .graph-container {
            margin-top: 50px;
            text-align: center;
        }

        canvas {
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registro y visualización de temperatura</h1>

        <!-- Formulario para insertar nueva temperatura -->
        <h2>Insertar Nueva Temperatura</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="temperatura">Temperatura (°C):</label>
            <input type="number" id="temperatura" name="temperatura" required>
            <button type="submit">Enviar</button>
        </form>

        <!-- PHP para insertar datos y visualizar la última temperatura -->
        <?php
        $hostname = "localhost";
        $username = "root";
        $password = "";
        $database = "esp32";

        $conn = mysqli_connect($hostname, $username, $password, $database);

        if (!$conn) {
            die("<p class='error'>Connection failed: " . mysqli_connect_error() . "</p>");
        }

        if (isset($_POST["temperatura"])) {
            $t = $_POST["temperatura"];
            $sql = "INSERT INTO lm35 (temperatura) VALUES ('$t')";

            if (mysqli_query($conn, $sql)) {
                echo "<p class='success'>Se ha guardado el dato</p>";
            } else {
                echo "<p class='error'>Error: " . $sql . "<br>" . mysqli_error($conn) . "</p>";
            }
        }

        $sql = "SELECT temperatura, tiempo FROM lm35 ORDER BY id DESC LIMIT 10"; // Limitar a las últimas 10 temperaturas para el gráfico
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<h2>Últimas Temperaturas Registradas</h2>";
            echo "<table>";
            echo "<tr><th>Temperatura (°C)</th><th>Hora de registro</th></tr>";
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr><td>" . $row["temperatura"] . "</td><td>" . $row["tiempo"] . "</td></tr>";
            }
            echo "</table>";

            // Gráfico de temperaturas
            echo "<div class='graph-container'>";
            echo "<canvas id='temperature-chart'></canvas>";
            echo "</div>";
        } else {
            echo "<p>No hay datos disponibles</p>";
        }

        mysqli_close($conn);
        ?>

        <!-- Aquí iría el resto de tu contenido PHP -->
    </div>

    <!-- Script para generar el gráfico -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Obtener los datos de temperatura desde PHP
        var temperatures = [
            <?php
            $conn = mysqli_connect($hostname, $username, $password, $database);
            $sql = "SELECT temperatura FROM lm35 ORDER BY id DESC LIMIT 10"; // Limitar a las últimas 10 temperaturas
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($result)) {
                echo $row["temperatura"] . ",";
            }
            mysqli_close($conn);
            ?>
        ];

        // Obtener las fechas de registro
        var labels = [
            <?php
            $conn = mysqli_connect($hostname, $username, $password, $database);
            $sql = "SELECT tiempo FROM lm35 ORDER BY id DESC LIMIT 10"; // Limitar a las últimas 10 fechas
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($result)) {
                echo "'" . $row["tiempo"] . "',";
            }
            mysqli_close($conn);
            ?>
        ];

        var ctx = document.getElementById('temperature-chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Temperaturas',
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1,
                    data: temperatures,
                    fill: true
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                }
            }
        });
    </script>
</body>
</html>
