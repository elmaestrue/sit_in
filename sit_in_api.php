<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

if (isset($_GET['id'])) {
    $student_id = $conn->real_escape_string($_GET['id']);
    $query = "SELECT * FROM sit_in_records WHERE student_id = '$student_id'";
} else {
    $query = "SELECT * FROM sit_in_records ORDER BY sit_in_time DESC";
}

$result = $conn->query($query);
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id" => $row['id'],
            "student_id" => $row['student_id'],
            "name" => $row['name'],
            "purpose" => $row['language'],
            "lab" => $row['laboratory'],
            "session" => date('Y-m-d H:i:s', strtotime($row['sit_in_time'])),
            "status" => $row['log_out_time'] ? 'Logged Out' : 'Active'
        ];
    }
} else {
    echo json_encode(["error" => "No records found"]);
    exit;
}

echo json_encode($data);
$conn->close();
?>
