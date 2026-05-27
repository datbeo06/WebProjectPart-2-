<?php
// settings.php
// Database connection configuration for SolarCore Energy
// Group Nick-Thu-1030-G03

$host = 'localhost';
$user = 'root';
$pwd = '';
$sql_db = 'solarcore_db';

// Keep connection failures readable instead of showing raw mysqli exceptions.
mysqli_report(MYSQLI_REPORT_OFF);

// Create connection using mysqli
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

// Check connection
if (!$conn) {
    die(
        "Database connection failed. Make sure MySQL is running in XAMPP and import database.sql into phpMyAdmin. " .
        "Expected database: " . htmlspecialchars($sql_db, ENT_QUOTES, 'UTF-8') . ". " .
        "MySQL said: " . htmlspecialchars(mysqli_connect_error(), ENT_QUOTES, 'UTF-8')
    );
}

// Set character set to utf8mb4 for rich multilingual content support
mysqli_set_charset($conn, "utf8mb4");
?>
