<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// DB connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch only active sit-ins
$sitins = $conn->query("SELECT * FROM sit_in_records WHERE log_out_time IS NULL ORDER BY sit_in_time DESC");

// Handle AJAX search
if (isset($_GET["q"])) {
    $search = $conn->real_escape_string($_GET["q"]);
    $sql = "SELECT student_id, name FROM sit_in_records WHERE student_id LIKE '%$search%' OR name LIKE '%$search%' ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    echo json_encode($result->num_rows > 0 ? $result->fetch_assoc() : ["error" => "No results found."]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Active Sit-in - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d47a1;">
    <div class="container-fluid">
        <a class="navbar-brand"><strong>College of Computer Studies Admin</strong></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link text-white" href="admin.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#searchModal">Search</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="students.php">Students</a></li>
                <li class="nav-item"><a class="nav-link text-white active" href="sit_in.php">Sit-in</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="ViewRecords.php">View Sit-in Records</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="report.php">Sit-in Reports</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="feedback_report.php">Feedback Reports</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Reservation</a></li>
            </ul>
        </div>
        <a href="logout.php" class="btn btn-warning text-dark">Log out</a>
    </div>
</nav>

<!-- SIT-IN TABLE -->
<div class="container mt-4">
    <h2 class="text-center mb-4">Current Sit-In</h2>
    <table id="sitInTable" class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Sit ID Number</th>
                <th>ID Number</th>
                <th>Name</th>
                <th>Purpose</th>
                <th>Sit Lab</th>
                <th>Session</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $sitins->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['student_id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['language']) ?></td>
                    <td><?= htmlspecialchars($row['laboratory']) ?></td>
                    <td><?= $row['session'] ?? 'N/A' ?></td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td>
                        <a href="logout.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Log Out</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- SEARCH MODAL -->
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

<!-- DATATABLE & SEARCH SCRIPT -->
<script>
$(document).ready(function() {
    $('#sitInTable').DataTable();

    $('#searchButton').click(function() {
        let query = $('#searchInput').val().trim();
        if (query === "") {
            $('#searchResults').html('<p class="text-danger">Please enter a Student ID or Name.</p>');
            return;
        }

        fetch("sit_in.php?q=" + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    $('#searchResults').html(`
                        <p><strong>ID:</strong> ${data.student_id}</p>
                        <p><strong>Name:</strong> ${data.name}</p>
                    `);
                } else {
                    $('#searchResults').html('<p class="text-danger">No results found.</p>');
                }
            })
            .catch(() => {
                $('#searchResults').html('<p class="text-danger">Error retrieving data.</p>');
            });
    });
});
</script>

</body>
</html>
