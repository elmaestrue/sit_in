<?php
include 'db.php';

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=sit_in_report.csv");

$output = fopen("php://output", "w");
fputcsv($output, array('Student ID', 'Sit-in Time', 'Log-out Time'));

$result = $conn->query("SELECT * FROM sit_in_records");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
