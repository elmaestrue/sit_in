<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];

// Get student_id from studentinfo using username
$user_stmt = $conn->prepare("SELECT student_id FROM studentinfo WHERE username = ?");
$user_stmt->bind_param("s", $username);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    die("Error: Student not found.");
}

$student_id = $user['student_id'];

// Get remaining_sessions from students table
$session_stmt = $conn->prepare("SELECT remaining_sessions FROM students WHERE student_id = ?");
$session_stmt->bind_param("s", $student_id);
$session_stmt->execute();
$session_result = $session_stmt->get_result();
$student = $session_result->fetch_assoc();

if (!$student) {
    die("Error: Student record not found.");
}

$remaining_sessions = $student['remaining_sessions'];

if ($remaining_sessions <= 0) {
    // Cannot logout, sessions exhausted
    echo "<script>alert('Logout denied: No remaining sit-in sessions left. Please contact admin.'); window.location.href = 'index.php';</script>";
    exit();
} else {
    // Deduct 1 session and update
    $new_remaining = $remaining_sessions - 1;
    $update_stmt = $conn->prepare("UPDATE students SET remaining_sessions = ? WHERE student_id = ?");
    $update_stmt->bind_param("is", $new_remaining, $student_id);
    $update_stmt->execute();
}

// Now destroy session
session_unset();
session_destroy();
header("Location: login.html");
exit();
?>
