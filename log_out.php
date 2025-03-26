<?php
include 'db.php'; // Connect to the database

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Update the sit-in record to mark as logged out
    $query = "UPDATE sit_in_records SET status='Logged Out' WHERE student_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        header("Location: admin.php"); // Redirect back to admin panel
        exit();
    } else {
        echo "Error logging out student.";
    }
}
?>
