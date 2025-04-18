<?php
session_start();

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Sit-in Records
$records_query = "SELECT id, student_id, name, language, laboratory, sit_in_time, log_out_time FROM sit_in_records ORDER BY sit_in_time DESC";
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
    <title>CCS | Sit-in Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        canvas {
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d47a1;">
    <div class="container-fluid">
        <a class="navbar-brand text-white"><strong>College of Computer Studies Admin</strong></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link text-white" href="admin.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#searchModal">Search</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="students.php">Students</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="sit_in.php">Sit-in</a></li>
                <li class="nav-item"><a class="nav-link text-white active" href="ViewRecords.php">View Sit-in Records</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="report.php">Sit-in Reports</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="feedback_report.php">Feedback Reports</a></li>
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
    <div class="row mb-4 justify-content-center">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">📊 Sit-in by Language</div>
                <div class="card-body text-center">
                    <canvas id="languageChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">🏢 Sit-in by Laboratory</div>
                <div class="card-body text-center">
                    <canvas id="labChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sit-in Records Table -->
    <div>
        <table id="recordsTable" class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Sit-in #</th>
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Purpose</th>
                    <th>Lab</th>
                    <th>Login</th>
                    <th>Logout</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sit_in_records as $record): ?>
                <tr>
                    <td><?= $record['id'] ?></td>
                    <td><?= $record['student_id'] ?></td>
                    <td><?= $record['name'] ?></td>
                    <td><?= $record['language'] ?></td>
                    <td><?= $record['laboratory'] ?></td>
                    <td><?= $record['sit_in_time'] ?></td>
                    <td><?= $record['log_out_time'] ?></td>
                    <td><?= date('Y-m-d', strtotime($record['sit_in_time'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Search Student Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Search Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchInput" class="form-control mb-2" placeholder="Enter ID or Name">
                <button class="btn btn-primary w-100" id="searchButton">Search</button>
                <div id="searchResults" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Sit In Modal -->
<div class="modal fade" id="sitInModal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <form action="sit_in_action.php" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sit In Form</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control mb-2" id="sit_student_id" name="student_id" readonly>
        <input type="text" class="form-control mb-2" id="sit_name" name="student_name" readonly>
        <input type="text" class="form-control mb-2" id="sit_purpose" name="purpose" placeholder="Purpose" required>
        <input type="text" class="form-control mb-2" id="sit_lab" name="lab" placeholder="Lab" required>
        <input type="text" class="form-control mb-2" id="sit_remaining" name="remaining_sessions" readonly>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Sit In</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#recordsTable').DataTable({
        order: [[7, 'desc']] // Sort by Date column (index 7) descending
    });

    $('#searchButton').on('click', function () {
        const query = $('#searchInput').val().trim();
        const resultDiv = $('#searchResults');

        if (!query) {
            resultDiv.html("<p class='text-danger'>Please enter a query.</p>");
            return;
        }

        fetch("search.php?q=" + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    resultDiv.html(`<p class="text-danger">${data.error}</p>`);
                } else {
                    resultDiv.html(`
                        <div class="card border-success">
                            <div class="card-body">
                                <p><strong>ID:</strong> ${data.student_id}</p>
                                <p><strong>Name:</strong> ${data.name}</p>
                                <p><strong>Lab:</strong> ${data.lab}</p>
                                <p><strong>Remaining Sessions:</strong> ${data.remaining_sessions}</p>
                                <button class="btn btn-success mt-2" id="openSitIn">✔ Create Sit-in Record</button>
                            </div>
                        </div>
                    `);

                    $('#openSitIn').on('click', function () {
                        $('#sit_student_id').val(data.student_id);
                        $('#sit_name').val(data.name);
                        $('#sit_lab').val(data.lab);
                        $('#sit_remaining').val(data.remaining_sessions);
                        $('#sit_purpose').val('');
                        new bootstrap.Modal(document.getElementById('sitInModal')).show();
                    });
                }
            })
            .catch(err => {
                resultDiv.html(`<p class="text-danger">Error: ${err.message}</p>`);
            });
    });

    const languageData = <?= json_encode($language_data); ?>;
    const languageLabels = languageData.map(item => item.language);
    const languageCounts = languageData.map(item => item.count);

    new Chart(document.getElementById('languageChart'), {
        type: 'pie',
        data: {
            labels: languageLabels,
            datasets: [{
                data: languageCounts,
                backgroundColor: ['#36A2EB', '#FF6384', '#FF9F40', '#4CAF50', '#8E44AD']
            }]
        }
    });

    const labData = <?= json_encode($laboratory_data); ?>;
    const labLabels = labData.map(item => item.laboratory);
    const labCounts = labData.map(item => item.count);

    new Chart(document.getElementById('labChart'), {
        type: 'pie',
        data: {
            labels: labLabels,
            datasets: [{
                data: labCounts,
                backgroundColor: ['#FF6384', '#36A2EB', '#FF9F40', '#4CAF50', '#8E44AD']
            }]
        }
    });
});
</script>
</body>
</html>
