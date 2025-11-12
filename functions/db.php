
<?php
// Live server database configuration
$servername = "localhost";
$username = "jemima.nhyira";
$password = "Wat_again143";
$dbname = "ecommerce_2025A_jemima_nhyira";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

