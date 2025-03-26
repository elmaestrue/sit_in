<?php
$servername = "localhost";
$username = "root"; // Default XAMPP user
$password = ""; // Leave empty if no password
$database = "test"; // Make sure this matches your actual DB name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
