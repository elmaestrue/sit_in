<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false]));
}

$id = $_GET['q'];
$result = $conn->query("SELECT * FROM students WHERE student_id = '$id'");

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "student_id" => $row["student_id"],
        "name" => $row["name"],
        "language" => $row["language"],
        "lab" => $row["lab"],
        "remaining_sessions" => $row["remaining_sessions"]
    ]);
} else {
    echo json_encode(["success" => false]);
}
$conn->close();
?>
