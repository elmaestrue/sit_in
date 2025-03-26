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

// Fetch sit-in records
$sitins = $conn->query("SELECT * FROM sit_in_records ORDER BY sit_in_time DESC");

// Handle search request
if (isset($_GET["q"])) {
    $search = $conn->real_escape_string($_GET["q"]);

    $sql = "SELECT student_id, name FROM sit_in_records WHERE student_id LIKE '%$search%' OR name LIKE '%$search%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "No results found."]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS | View Sit-in Records</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                <li class="nav-item"><a class="nav-link text-white" href="sit_in.php">Sit-in</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="ViewRecords.php">View Sit-in Records</a></li>
            </ul>
        </div>
        <a href="logout.php" class="btn btn-warning text-dark">Log out</a>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-4">
    <h2 class="text-center">Sit-In Records</h2>
    <table id="sitInRecordsTable" class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Sit ID Number</th>
                <th>ID Number</th>
                <th>Name</th>
                <th>Purpose</th>
                <th>Lab</th>
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
                <td><?= date('Y-m-d H:i:s', strtotime($row['sit_in_time'])) ?></td>
                <td><?= $row['log_out_time'] ? 'Logged Out' : 'Active' ?></td>
                <td>
                    <?php if (empty($row['log_out_time'])) { ?>
                        <a href="logout.php?id=<?= $row['student_id'] ?>" class="btn btn-danger btn-sm">Log Out</a>
                    <?php } else { ?>
                        <span class="badge bg-secondary">Logged Out</span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
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

<script>
    $(document).ready(function() {
        $('#sitInRecordsTable').DataTable();
    });

    document.getElementById("searchButton").addEventListener("click", function() {
        let query = document.getElementById("searchInput").value.trim();
        let searchResults = document.getElementById("searchResults");

        if (query === "") {
            searchResults.innerHTML = '<p class="text-danger">Please enter a Student ID or Name.</p>';
            return;
        }

        fetch("ViewRecords.php?q=" + encodeURIComponent(query))  // Adjusted to fetch from ViewRecords.php
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    searchResults.innerHTML = `
                        <p><strong>ID:</strong> ${data.student_id}</p>
                        <p><strong>Name:</strong> ${data.name}</p>
                    `;
                } else {
                    searchResults.innerHTML = '<p class="text-danger">No results found.</p>';
                }
            })
            .catch(() => {
                searchResults.innerHTML = '<p class="text-danger">Error retrieving data.</p>';
            });
    });
</script>

</body>
</html>
