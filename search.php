<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

// Validate input
if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode(['error' => 'Missing search query.']);
    exit;
}

$q = $conn->real_escape_string($_GET['q']);

// Query for matching student
$sql = "SELECT student_id, name, laboratory AS lab, remaining_sessions 
        FROM students 
        WHERE student_id LIKE '%$q%' OR name LIKE '%$q%' 
        LIMIT 1";

$result = $conn->query($sql);

// Return result
if ($result && $result->num_rows > 0) {
    $student = $result->fetch_assoc();

    // Handle null or empty lab field
    if (is_null($student['lab']) || $student['lab'] === '') {
        $student['lab'] = 'Not Assigned';
    }

    echo json_encode($student);
} else {
    echo json_encode(['error' => 'Student not found.']);
}

$conn->close();
