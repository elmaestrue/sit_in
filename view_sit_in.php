<?php
include 'db.php';
$result = $conn->query("SELECT * FROM sit_in_records WHERE log_out_time IS NULL");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Sit-in Records</title>
</head>
<body>
    <h2>Currently Sitting-in Students</h2>
    <table border="1">
        <tr>
            <th>Student ID</th>
            <th>Sit-in Time</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['student_id'] ?></td>
                <td><?= $row['sit_in_time'] ?></td>
                <td><a href="log_out.php?id=<?= $row['id'] ?>">Log Out</a></td>
                <td><a href="export.php"><button>Export CSV</button></a></td>
                <td><a href="reset_session.php?student_id=<?= $row['student_id'] ?>"><button>Reset Session</button></a></td>
                                                                 
            </tr>
        <?php } ?>
    </table>
</body>
</html>
