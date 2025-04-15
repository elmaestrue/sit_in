<?php
session_start();
$conn = new mysqli("localhost", "root", "", "test");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure required POST fields are set
if (
    isset($_POST['student_id'], $_POST['student_name'], $_POST['purpose'], $_POST['lab']) &&
    !empty($_POST['student_id'])
) {
    $student_id = $_POST['student_id'];
    $name = $_POST['student_name'];
    $purpose = $_POST['purpose'];
    $lab = $_POST['lab'];

    // Fetch current remaining sessions
    $query = "SELECT remaining_sessions FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $remaining = (int)$row['remaining_sessions'];

        if ($remaining <= 0) {
            $_SESSION['error'] = "This student has no remaining sessions.";
            header("Location: admin.php");
            exit;
        }

        $new_remaining = $remaining - 1;

        // Insert sit-in record
        $insert = "INSERT INTO sit_in_records (student_id, name, lab, purpose, remaining_sessions) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert);
        $stmt_insert->bind_param("isssi", $student_id, $name, $lab, $purpose, $new_remaining);
        $stmt_insert->execute();

        // Update student's remaining sessions
        $update = "UPDATE students SET remaining_sessions = ? WHERE student_id = ?";
        $stmt_update = $conn->prepare($update);
        $stmt_update->bind_param("ii", $new_remaining, $student_id);
        $stmt_update->execute();

        $_SESSION['success'] = "Sit-in recorded successfully!";
    } else {
        $_SESSION['error'] = "Student not found.";
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid sit-in data submitted.";
}

$conn->close();
header("Location: admin.php");
exit;
?>
