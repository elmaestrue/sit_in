<?php
session_start();

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test"; // Change this to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Sit-in Records
$records_query = "SELECT id, student_id, name, language, laboratory, sit_in_time, log_out_time FROM sit_in_records";
$records_result = $conn->query($records_query);
$sit_in_records = [];
while ($row = $records_result->fetch_assoc()) {
    $sit_in_records[] = $row;
}

// Fetch Data for Charts
$language_query = "SELECT language, COUNT(*) as count FROM sit_in_records GROUP BY language";
$language_result = $conn->query($language_query);
$language_data = [];
while ($row = $language_result->fetch_assoc()) {
    $language_data[] = $row;
}

$laboratory_query = "SELECT laboratory, COUNT(*) as count FROM sit_in_records GROUP BY laboratory";
$laboratory_result = $conn->query($laboratory_query);
$laboratory_data = [];
while ($row = $laboratory_result->fetch_assoc()) {
    $laboratory_data[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS | Sit-in Records</title>
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
    <h2 class="text-center">Current Sit-in Records</h2>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">📊 Sit-in by Language</div>
                <div class="card-body">
                    <canvas id="languageChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">🏢 Sit-in by Laboratory</div>
                <div class="card-body">
                    <canvas id="labChart"></canvas>
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
            </div>
        </div>
    </div>
</div>

<!-- Sit-in Form Modal -->
<div class="modal fade" id="sitInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sit In Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <label>ID Number:</label>
                    <input type="text" id="idNumber" class="form-control" readonly>

                    <label>Student Name:</label>
                    <input type="text" id="studentName" class="form-control" readonly>

                    <label>Purpose:</label>
                    <input type="text" id="purpose" class="form-control" readonly>

                    <label>Lab:</label>
                    <input type="text" id="lab" class="form-control">

                    <label>Remaining Sessions:</label>
                    <input type="text" id="remainingSessions" class="form-control">

                    <button class="btn btn-primary w-100 mt-3">Sit In</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    let languageData = <?= json_encode($language_data); ?>;
    let languageLabels = languageData.map(item => item.language);
    let languageCounts = languageData.map(item => item.count);

    new Chart(document.getElementById('languageChart'), {
        type: 'pie',
        data: { labels: languageLabels, datasets: [{ data: languageCounts, backgroundColor: ['#36A2EB', '#FF6384', '#FF9F40'] }] }
    });

    let labData = <?= json_encode($laboratory_data); ?>;
    let labLabels = labData.map(item => item.laboratory);
    let labCounts = labData.map(item => item.count);

    new Chart(document.getElementById('labChart'), {
        type: 'pie',
        data: { labels: labLabels, datasets: [{ data: labCounts, backgroundColor: ['#FF6384', '#36A2EB', '#FF9F40'] }] }
    });
});

document.getElementById("searchButton").addEventListener("click", function() {
    let query = document.getElementById("searchInput").value.trim();

    fetch("search.php?q=" + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                document.getElementById("idNumber").value = data.student_id;
                document.getElementById("studentName").value = data.name;
                document.getElementById("purpose").value = data.language;
                document.getElementById("lab").value = data.laboratory;
                new bootstrap.Modal(document.getElementById('sitInModal')).show();
            }
        });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
