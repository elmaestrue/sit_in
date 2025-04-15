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

// Fetch feedback records (latest first)
$query = "
    SELECT 
        f.student_id,
        s.name AS fullname,
        s.course,
        f.laboratory,
        f.date,
        f.message,
        sr.time_in,
        sr.time_out
    FROM feedback f
    LEFT JOIN students s ON f.student_id = s.student_id
    LEFT JOIN sit_in_records sr ON f.student_id = sr.student_id AND DATE(f.date) = DATE(sr.date)
    ORDER BY f.date DESC
";
$result = $conn->query($query);
$feedbacks = [];
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
$conn->close();

// Foul word detection
$foul_words = ['stupid', 'idiot', 'dumb', 'fool', 'ugly', 'nonsense', 
                'bogo', 'yawa', 'atay', 'piste', 'kayat', 'boang', 'animal', 'tae']; // Add more as needed
function containsFoulWords($message, $foul_words) {
    foreach ($foul_words as $word) {
        if (stripos($message, $word) !== false) {
            return true;
        }
    }
    return false;
}
$foul_found = false;
foreach ($feedbacks as $fb) {
    if (containsFoulWords($fb['message'], $foul_words)) {
        $foul_found = true;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CCS | Feedback Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .table td, .table th {
            vertical-align: middle;
            text-align: center;
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
                <li class="nav-item"><a class="nav-link text-white" href="ViewRecords.php">View Sit-in Records</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="report.php">Sit-in Reports</a></li>
                <li class="nav-item"><a class="nav-link text-white active" href="feedback_report.php">Feedback Reports</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Reservation</a></li>
            </ul>
        </div>
        <a href="logout.php" class="btn btn-warning text-dark">Log out</a>
    </div>
</nav>

<!-- Container -->
<div class="container mt-4">
    <h2 class="text-center mb-4">Feedback Report</h2>

    <?php if ($foul_found): ?>
        <div class="alert alert-danger text-center fw-bold">
            ⚠️ Some feedbacks contain foul language. Please review them.
        </div>
    <?php endif; ?>

    <button class="btn btn-secondary mb-3" onclick="window.print()">🖨️ Print</button>

    <div class="table-responsive">
        <table id="feedbackTable" class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Student ID Number</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Laboratory</th>
                    <th>Date & Time Submitted</th>
                    <th>Message</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                    <?php $isFlagged = containsFoulWords($feedback['message'], $foul_words); ?>
                    <tr class="<?= $isFlagged ? 'table-danger' : '' ?>">
                        <td><?= htmlspecialchars($feedback['student_id']) ?></td>
                        <td><?= htmlspecialchars($feedback['fullname']) ?></td>
                        <td><?= htmlspecialchars($feedback['course']) ?></td>
                        <td><?= htmlspecialchars($feedback['laboratory']) ?></td>
                        <td>
                            <?php
                                $datetime = $feedback['date'];
                                if (!empty($datetime) && $datetime !== '0000-00-00 00:00:00' && strtotime($datetime)) {
                                    echo htmlspecialchars(date('M j, Y g:i A', strtotime($datetime)));
                                } else {
                                    echo 'N/A';
                                }
                            ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($feedback['message']) ?>
                            <?php if ($isFlagged): ?>
                                <span class="badge bg-danger ms-2">⚠️ Foul Language Detected</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $time_in = $feedback['time_in'];
                                echo !empty($time_in) ? htmlspecialchars(date('M j, Y g:i A', strtotime($time_in))) : 'N/A';
                            ?>
                        </td>
                        <td>
                            <?php
                                $time_out = $feedback['time_out'];
                                echo !empty($time_out) ? htmlspecialchars(date('M j, Y g:i A', strtotime($time_out))) : 'N/A';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Search Modal -->
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#feedbackTable').DataTable();

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
});
</script>

</body>
</html>
