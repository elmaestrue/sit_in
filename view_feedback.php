<?php
include 'db.php';
$result = $conn->query("SELECT f.*, s.name, s.course FROM feedback f JOIN students s ON f.student_id = s.student_id ORDER BY f.created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Feedback</title>
</head>
<body>
    <h2>Student Feedback</h2>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Course</th>
            <th>Feedback</th>
            <th>Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['name'] ?></td>
                <td><?= $row['course'] ?></td>
                <td><?= $row['message'] ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
