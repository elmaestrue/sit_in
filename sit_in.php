<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];

    // Check if student exists
    $checkStudent = $conn->query("SELECT * FROM students WHERE student_id = '$student_id'");
    if ($checkStudent->num_rows > 0) {
        // Register sit-in
        $conn->query("INSERT INTO sit_in_records (student_id) VALUES ('$student_id')");
        echo "Sit-in registered successfully!";
    } else {
        echo "Student ID not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Sit-in</title>
</head>
<body>
    <h2>Register Sit-in</h2>
    <form method="POST">
        <label>Student ID:</label>
        <input type="number" name="student_id" required>
        <button type="submit">Sit-in</button>
    </form>
</body>
</html>
