<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $student_id = $conn->real_escape_string($_GET['id']);

    // Update the log_out_time to the current timestamp
    $sql = "UPDATE sit_in_records SET log_out_time = NOW() WHERE student_id = '$student_id' AND log_out_time IS NULL";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Student successfully logged out.";
    } else {
        $_SESSION['message'] = "Error logging out: " . $conn->error;
    }
}

$conn->close();
header("Location: admin.php"); // Redirect back to the main page
exit();
?>
