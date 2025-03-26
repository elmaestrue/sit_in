<?php
include 'db.php';
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Announcements</title>
</head>
<body>
    <h2>Recent Announcements</h2>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <div>
            <h3><?= $row['title'] ?></h3>
            <p><?= $row['content'] ?></p>
            <small>Posted on: <?= $row['created_at'] ?></small>
        </div>
        <hr>
    <?php } ?>
</body>
</html>
