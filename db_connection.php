<?php
// Database connection details - change these to match your setup
$host = 'localhost';
$db_name  = 'Lakvisit'; // <-- Your actual database name
$user = 'root';       // XAMPP default username
$pass = '';           // XAMPP default password (empty by default)

// Create connection
$conn = new mysqli($host, $user, $pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>