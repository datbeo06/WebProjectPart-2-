<?php
// settings.php
// Database connection configuration for SolarCore Energy
// Group Nick-Thu-1030-G03

$host = 'localhost';
$user = 'root';
$pwd = '';
$sql_db = 'solarcore_db';

// Create connection using mysqli
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character set to utf8mb4 for rich multilingual content support
mysqli_set_charset($conn, "utf8mb4");
?>
