<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch announcements
$ann_query = "SELECT * FROM announcements ORDER BY date_posted DESC";
$ann_result = $conn->query($ann_query);
$announcements = [];

while ($row = $ann_result->fetch_assoc()) {
    $announcements[] = $row;
}

$conn->close();

echo json_encode($announcements);
?>
