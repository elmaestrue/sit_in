<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("UPDATE sit_in_records SET log_out_time = NOW() WHERE id = '$id'");
    echo "Student logged out successfully!";
    header("Location: view_sit_in.php");
}
?>
