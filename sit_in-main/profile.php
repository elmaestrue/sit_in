<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - CCS Sit-In Monitoring</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
    <p>Profile Information:</p>
    <p>Email: <?php echo $_SESSION['email']; ?></p>
    <a href="edit_profile.php">Edit Profile</a>
    <br><br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
