<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lab = $_POST['laboratory'];
    $result = $conn->query("SELECT * FROM sit_in_records WHERE laboratory = '$lab'");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Reports</title>
</head>
<body>
    <h2>Select Laboratory</h2>
    <form method="POST">
        <select name="laboratory" required>
            <option value="Lab A">Lab A</option>
            <option value="Lab B">Lab B</option>
            <option value="Lab C">Lab C</option>
        </select>
        <button type="submit">Generate</button>
    </form>

    <?php if (isset($result)) { ?>
        <h2>Report for <?= htmlspecialchars($lab) ?></h2>
        <table border="1">
            <tr>
                <th>Student ID</th>
                <th>Sit-in Time</th>
                <th>Log-out Time</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['student_id'] ?></td>
                    <td><?= $row['sit_in_time'] ?></td>
                    <td><?= $row['log_out_time'] ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</body>
</html>
