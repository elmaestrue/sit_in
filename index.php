<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];

// Step 1: Get student info from studentinfo table
$user_stmt = $conn->prepare("SELECT * FROM studentinfo WHERE username = ?");
$user_stmt->bind_param("s", $username);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    die("User not found in studentinfo.");
}

$student_id = $user['student_id'] ?? null;

if (!$student_id) {
    die("Student ID not found in studentinfo.");
}

// Step 2: Get remaining_sessions from students table
$student_stmt = $conn->prepare("SELECT remaining_sessions FROM students WHERE student_id = ?");
$student_stmt->bind_param("s", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student = $student_result->fetch_assoc();
$remaining_sessions = $student ? $student['remaining_sessions'] : 'N/A';

// Step 3: Get announcements
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY posted_at DESC LIMIT 5");

// Step 4: Get session logs
$logs_stmt = $conn->prepare("SELECT * FROM sit_in_records WHERE student_id = ? ORDER BY sit_in_time DESC LIMIT 10");
$logs_stmt->bind_param("s", $student_id);
$logs_stmt->execute();
$logs_result = $logs_stmt->get_result();

// Step 5: Get feedbacks (using submitted_at)
$feedback_stmt = $conn->prepare("SELECT * FROM feedback WHERE student_id = ? ORDER BY submitted_at DESC LIMIT 5");
$feedback_stmt->bind_param("s", $student_id);
$feedback_stmt->execute();
$feedback_result = $feedback_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        header {
            background-color: #0D47A1;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .menuicn {
            width: 30px;
            cursor: pointer;
            margin-right: 10px;
        }
        .sidebar {
            background-color: #0D47A1;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .sidebar img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        .navcontainer {
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            background-color: #2c3e50;
            transition: left 0.3s ease-in-out;
            z-index: 1100;
            padding-top: 20px;
        }
        .navcontainer.open {
            left: 0;
        }
        .nav-option {
            padding: 15px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .nav-option:hover {
            background-color: #34495e;
            transform: translateX(10px);
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 1050;
        }
        .overlay.show {
            display: block;
        }
        .announcement {
            background: #fff;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 4px solid #0D47A1;
        }
    </style>
</head>
<body>

<header>
    <div style="display: flex; align-items: center;">
        <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210182541/Untitled-design-(30).png" class="menuicn me-3" alt="menu-icon">
        <h4 class="m-0">Welcome, <?= htmlspecialchars($user['firstname']) ?>!</h4>
    </div>
</header>

<div class="overlay"></div>

    <!-- Sidebar Overlay -->
    <div class="overlay"></div>

<!-- Sidebar Navigation -->
<div class="navcontainer">
    <nav class="nav">
        <div class="nav-upper-options">
            <div class="nav-option">
                <a href="index.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Home</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="edit.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Profile</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="edit.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Edit</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="index.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">View Announcement</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="index.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Sit-in Rules</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="index.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Lab Rules & Regulations</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="history.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Sit-in History</h3> 
                </a>
                
            </div>
            <div class="nav-option"><h3 class="nav-text">Reservation</h3></div>
            <div class="nav-option"><h3 class="nav-text">View Remaining Session</h3></div>
            <div class="nav-option">
                <a href="logout.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Log-Out</h3>
                </a>
            </div>
        </div>
    </nav>
</div>

<!-- CSS -->
<style>
    .nav-text {
        font-size: 14px; /* Adjust the size as needed */
    }
</style>

<div class="container-fluid mt-3">
    <div class="row">
        <!-- Sidebar: Student Info -->
        <div class="col-md-3" style="margin-left: 75px;">
            <div class="sidebar">
                <h5>Student Info</h5>
                <img src="<?= htmlspecialchars($user['profile_image'] ?? 'lob.jpg') ?>" alt="Profile Image">
                <hr style="border-color: white;">
                <p><strong>Name:</strong><br><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></p>
                <p><strong>Course:</strong><br><?= htmlspecialchars($user['course']) ?></p>
                <p><strong>Year:</strong><br><?= htmlspecialchars($user['yearlevel']) ?></p>
                <p><strong>Email:</strong><br><?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Address:</strong><br><?= htmlspecialchars($user['address']) ?></p>
                <p><strong>Remaining Sessions:</strong> <?= htmlspecialchars($remaining_sessions) ?></p>
            </div>
        </div>

        <!-- Main: Announcements, Logs, Feedback -->
        <div class="col-md-6">
            <h4>📢 Announcements</h4>
            <?php while ($row = mysqli_fetch_assoc($announcements)) { ?>
                <div class="announcement">
                    <strong><?= date("M d, Y", strtotime($row['posted_at'])) ?></strong>
                    <p><?= htmlspecialchars($row['message']) ?></p>
                </div>
            <?php } ?>

            <hr>
            <h4>🕒 Session Logs</h4>
            <ul class="list-group">
                <?php while ($log = $logs_result->fetch_assoc()) { ?>
                    <li class="list-group-item">
                        <strong><?= htmlspecialchars($log['laboratory']) ?>:</strong>
                        <?= date("M d, Y - h:i A", strtotime($log['sit_in_time'])) ?>
                        to <?= $log['log_out_time'] ? date("h:i A", strtotime($log['log_out_time'])) : 'N/A' ?>
                    </li>
                <?php } ?>
            </ul>

            <hr>
            <h4>📝 Your Feedback</h4>
            <ul class="list-group">
                <?php while ($fb = $feedback_result->fetch_assoc()) { ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($fb['message']) ?><br>
                        <small class="text-muted"><?= date("M d, Y - h:i A", strtotime($fb['submitted_at'])) ?></small>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <!-- Optional Column -->
        <div class="col-md-3">
        </div>
    </div>
</div>

<!-- Sidebar Toggle -->
<script>
    let menuIcon = document.querySelector(".menuicn");
    let sidebar = document.querySelector(".navcontainer");
    let overlay = document.querySelector(".overlay");

    if (menuIcon) {
        menuIcon.addEventListener("click", () => {
            sidebar.classList.add("open");
            overlay.classList.add("show");
        });
    }

    if (overlay) {
        overlay.addEventListener("click", () => {
            sidebar.classList.remove("open");
            overlay.classList.remove("show");
        });
    }
</script>

</body>
</html>
