<?php
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) { die("Connection failed"); }

$id = $_POST['id'];
$purpose = $_POST['purpose'];
$lab = $_POST['lab'];
$sessions = $_POST['sessions'];

$conn->query("INSERT INTO sit_in_records (student_id, purpose, lab, session) VALUES ('$id', '$purpose', '$lab', '$sessions')");
echo "Sit-In Recorded Successfully!";
$conn->close();
?>
