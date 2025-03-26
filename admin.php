<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch sit-in statistics
$stats_query = "SELECT language, COUNT(*) as count FROM sit_in_records GROUP BY language";
$stats_result = $conn->query($stats_query);
$stats_data = [];
while ($row = $stats_result->fetch_assoc()) {
    $stats_data[] = $row;
}

// Get total students
$total_students_query = "SELECT COUNT(*) as total FROM students";
$total_students_result = $conn->query($total_students_query);
$total_students = $total_students_result->fetch_assoc()['total'];

// Get total sit-in count
$total_sit_in_query = "SELECT COUNT(*) as total FROM sit_in_records";
$total_sit_in_result = $conn->query($total_sit_in_query);
$total_sit_in = $total_sit_in_result->fetch_assoc()['total'];

// Count unique students who have sit-in records
$current_sit_in_query = "SELECT COUNT(DISTINCT student_id) as total FROM sit_in_records";
$current_sit_in_result = $conn->query($current_sit_in_query);
$current_sit_in = $current_sit_in_result->fetch_assoc()['total'];

// Fetch announcements
$ann_query = "SELECT * FROM announcements ORDER BY date_posted DESC";
$ann_result = $conn->query($ann_query);
$announcements = [];
while ($row = $ann_result->fetch_assoc()) {
    $announcements[] = $row;
}

// Handle announcement submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['announcement'])) {
    $announcement = $conn->real_escape_string($_POST['announcement']);
    $stmt = $conn->prepare("INSERT INTO announcements (message, date_posted) VALUES (?, NOW())");
    $stmt->bind_param("s", $announcement);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS | Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d47a1; padding: 10px;">
    <div class="container-fluid">
        <a class="navbar-brand text-white"><strong>College of Computer Studies Admin</strong></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link text-white" href="admin.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#searchModal">Search</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Students</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="sit_in.php">Sit-in</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="ViewRecords.php">View Sit-in Records</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Sit-in Reports</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Feedback Reports</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Reservation</a></li>
            </ul>
        </div>
        <a href="logout.php" class="btn btn-warning text-dark">Log out</a>
    </div>
</nav>

<!-- Main Container -->
<div class="container mt-4">
    <div class="row">
        <!-- Statistics Section -->
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">📊 Statistics</div>
                <div class="card-body">
                    <p><strong>Students Registered:</strong> <?= $total_students ?></p>
                    <p><strong>Currently Sit-in:</strong> <?= $current_sit_in ?></p>
                    <p><strong>Total Sit-in:</strong> <?= $total_sit_in ?></p>
                    <canvas id="statsChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Announcements Section -->
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">📢 Announcement</div>
                <div class="card-body">
                    <form method="POST">
                        <textarea class="form-control mb-2" name="announcement" placeholder="New Announcement"></textarea>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                    <h5 class="mt-3">📌 Posted Announcements</h5>
                    <ul class="list-group">
                        <?php foreach ($announcements as $ann) { ?>
                            <li class="list-group-item">
                                <strong>CCS Admin | <?= date("Y-M-d", strtotime($ann['date_posted'])) ?></strong><br>
                                <?= nl2br(htmlspecialchars($ann['message'])) ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Student Modal -->
<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchInput" class="form-control mb-2" placeholder="Enter ID or Name">
                <button class="btn btn-primary w-100" id="searchButton">Search</button>
                <div id="searchResults" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Sit-In Form Modal -->
<div class="modal fade" id="sitInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sit In Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="sit_in_submit.php" method="POST">
                    <div class="mb-2">
                        <label><strong>ID Number:</strong></label>
                        <input type="text" class="form-control" name="student_id" id="sitInId" readonly>
                    </div>
                    <div class="mb-2">
                        <label><strong>Student Name:</strong></label>
                        <input type="text" class="form-control" name="name" id="sitInName" readonly>
                    </div>
                    <div class="mb-2">
                        <label><strong>Purpose:</strong></label>
                        <input type="text" class="form-control" name="purpose" id="sitInPurpose" required>
                    </div>
                    <div class="mb-2">
                        <label><strong>Lab:</strong></label>
                        <input type="text" class="form-control" name="lab" id="sitInLab" required>
                    </div>
                    <div class="mb-2">
                        <label><strong>Remaining Sessions:</strong></label>
                        <input type="number" class="form-control" name="sessions" id="sitInSessions" required>
                    </div>
                    <button type="submit" name="sitInSubmit" class="btn btn-primary w-100">Sit In</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Pie Chart (Unchanged)
    let data = <?= json_encode($stats_data); ?>;
    let labels = data.map(item => item.language);
    let values = data.map(item => item.count);

    new Chart(document.getElementById('statsChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{ data: values, backgroundColor: ['#36A2EB', '#FF6384', '#FF9F40', '#FFCD56', '#4BC0C0'] }]
        }
    });

    // Search Functionality
    document.getElementById("searchButton").addEventListener("click", function() {
        let query = document.getElementById("searchInput").value.trim();
        let searchResults = document.getElementById("searchResults");

        if (query === "") {
            searchResults.innerHTML = "<p class='text-danger'>Please enter a Student ID or Name.</p>";
            return;
        }

        fetch("search.php?q=" + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    searchResults.innerHTML = `<p class="text-danger">${data.error}</p>`;
                } else {
                    // Fill Sit-In Form fields
                    document.getElementById("sitInId").value = data.student_id;
                    document.getElementById("sitInName").value = data.name;
                    document.getElementById("sitInPurpose").value = data.language;
                    document.getElementById("sitInLab").value = data.laboratory;
                    document.getElementById("sitInSessions").value = data.remaining_sessions;

                    // Show Sit-In Modal
                    let sitInModal = new bootstrap.Modal(document.getElementById("sitInModal"));
                    sitInModal.show();
                }
            })
            .catch(error => {
                searchResults.innerHTML = "<p class='text-danger'>Failed to fetch data.</p>";
            });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
