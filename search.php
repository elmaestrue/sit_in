<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test"; // Make sure this matches your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

if (isset($_GET['q'])) {
    $query = $conn->real_escape_string($_GET['q']);
    
    $sql = "SELECT student_id, name, language, laboratory FROM sit_in_records 
            WHERE student_id LIKE '%$query%' OR name LIKE '%$query%' LIMIT 1";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "No student found"]);
    }
}

$conn->close();
?>
