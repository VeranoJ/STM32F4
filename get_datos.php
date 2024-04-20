<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "esp32";

// Crear conexión
$conn = mysqli_connect($hostname, $username, $password, $database);

// Verificar conexión
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Consulta SQL para obtener el último dato de temperatura insertado en la tabla lm35
$sql = "SELECT temperatura FROM lm35 ORDER BY id DESC LIMIT 1";

// Ejecutar la consulta
$result = mysqli_query($conn, $sql);

// Verificar si hay resultados
if (mysqli_num_rows($result) > 0) {
    // Obtener el dato de temperatura
    $row = mysqli_fetch_assoc($result);
    $temperatura = $row['temperatura'];

    // Devolver el dato de temperatura como respuesta
    echo $temperatura;
} else {
    echo "No se encontraron datos.";
}

// Cerrar la conexión
mysqli_close($conn);
?>
