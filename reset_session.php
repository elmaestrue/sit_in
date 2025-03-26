<?php
include 'db.php';

if (isset($_GET['student_id'])) {
    $id = $_GET['student_id'];
    $stmt = $conn->prepare("UPDATE students SET session = 30 WHERE student_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Session reset to 30!'); window.location.href='view_sit_in.php';</script>";
    } else {
        echo "<script>alert('Error resetting session!');</script>";
    }

    $stmt->close();
}

$conn->close();
?>
