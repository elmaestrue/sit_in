<?php
$host = "localhost";
$user = "root"; // Change if using different username
$password = ""; // Change if using a password
$database = "sit_in_system";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
