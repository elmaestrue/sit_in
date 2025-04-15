<?php
$conn = new mysqli("localhost", "root", "", "test");
$id = $_GET['id'];
$res = $conn->query("SELECT remaining_session FROM students WHERE student_id = '$id'");
if ($res && $res->num_rows > 0) {
    echo json_encode($res->fetch_assoc());
} else {
    echo json_encode(["remaining_session" => 30]);
}
$conn->close();
?>
