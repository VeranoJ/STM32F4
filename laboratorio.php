<?php
$hostname = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "esp32"; 

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) { 
	die("Connection failed: " . mysqli_connect_error()); 
} 

echo "Estamos conectados mi perro<br>"; 

if(isset($_POST["temperatura"]) ) {

	$t = $_POST["temperatura"];


	$sql = "INSERT INTO lm35 (temperatura) VALUES (".$t.")"; 

	if (mysqli_query($conn, $sql)) { 
		echo "\nSe ha guardado el dato"; 
	} else { 
		echo "Error: " . $sql . "<br>" . mysqli_error($conn); 
	}
}

?>