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

// Stats
$stats_query = "SELECT language, COUNT(*) as count FROM sit_in_records GROUP BY language";
$stats_result = $conn->query($stats_query);
$stats_data = [];
while ($row = $stats_result->fetch_assoc()) {
    $stats_data[] = $row;
}
$total_students = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
$total_sit_in = $conn->query("SELECT COUNT(*) as total FROM sit_in_records")->fetch_assoc()['total'];
$current_sit_in = $conn->query("SELECT COUNT(DISTINCT student_id) as total FROM sit_in_records")->fetch_assoc()['total'];

// Post announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['announcement'])) {
    $announcement = $conn->real_escape_string($_POST['announcement']);
    $stmt = $conn->prepare("INSERT INTO announcements (message, date_posted) VALUES (?, NOW())");
    $stmt->bind_param("s", $announcement);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// Edit announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id']) && isset($_POST['edit_message'])) {
    $id = intval($_POST['edit_id']);
    $msg = $conn->real_escape_string($_POST['edit_message']);
    $conn->query("UPDATE announcements SET message='$msg' WHERE id=$id");
    header("Location: admin.php");
    exit();
}

// Delete announcement
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM announcements WHERE id=$id");
    header("Location: admin.php");
    exit();
}

// Fetch announcements
$ann_result = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
$announcements = [];
while ($row = $ann_result->fetch_assoc()) {
    $announcements[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body style="background-color: #f5f5f5;">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d47a1;">
    <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold">College of Computer Studies Admin</a>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link text-white" href="admin.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#searchModal">Search</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="students.php">Students</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="sit_in.php">Sit-in</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="ViewRecords.php">View Sit-in Records</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="report.php">Sit-in Reports</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="feedback_report.php">Feedback Reports</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Reservation</a></li>
            </ul>
        </div>
        <a href="logout.php" class="btn btn-warning text-dark">Log out</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <!-- Stats -->
        <div class="col-md-6 mb-4">
            <div class="card border-primary shadow-sm">
                <div class="card-header bg-primary text-white">📊 Sit-in Statistics</div>
                <div class="card-body">
                    <p><strong>Students Registered:</strong> <?= $total_students ?></p>
                    <p><strong>Currently Sit-in:</strong> <?= $current_sit_in ?></p>
                    <p><strong>Total Sit-in:</strong> <?= $total_sit_in ?></p>
                    <canvas id="statsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="col-md-6 mb-4">
            <div class="card border-primary shadow-sm">
                <div class="card-header bg-primary text-white">📢 Announcements</div>
                <div class="card-body">
                    <form method="POST">
                        <textarea class="form-control mb-2" name="announcement" rows="3" placeholder="New Announcement..." required></textarea>
                        <button type="submit" class="btn btn-success w-100">Post</button>
                    </form>
                    <h6 class="mt-4">📌 Recent</h6>
                    <ul class="list-group">
                        <?php foreach ($announcements as $ann): ?>
                            <li class="list-group-item">
                                <strong>Admin | <?= date("Y-m-d", strtotime($ann['date_posted'])) ?></strong><br>
                                <?= nl2br(htmlspecialchars($ann['message'])) ?>
                                <div class="mt-2 d-flex justify-content-end">
                                    <button class="btn btn-sm btn-warning me-2" onclick="editAnnouncement(<?= $ann['id'] ?>, `<?= htmlspecialchars($ann['message'], ENT_QUOTES) ?>`)">Edit</button>
                                    <a href="?delete=<?= $ann['id'] ?>" onclick="return confirm('Delete this announcement?')" class="btn btn-sm btn-danger">Delete</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <form method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Announcement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <textarea class="form-control" name="edit_message" id="edit_message" rows="4" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog">
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

<!-- Sit In Form Modal -->
<div class="modal fade" id="sitInModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="sit_in_action.php" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sit In Form</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">ID Number</label>
          <input type="text" class="form-control" id="sit_student_id" name="student_id" readonly>
        </div>
        <div class="mb-3">
          <label class="form-label">Student Name</label>
          <input type="text" class="form-control" id="sit_name" name="student_name" readonly>
        </div>
        <div class="mb-3">
          <label class="form-label">Purpose</label>
          <input type="text" class="form-control" id="sit_purpose" name="purpose" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Lab</label>
          <input type="text" class="form-control" id="sit_lab" name="lab" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Remaining Sessions</label>
          <input type="text" class="form-control" id="sit_remaining" name="remaining_sessions" readonly>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Sit In</button>
      </div>
    </form>
  </div>
</div>


<!-- Scripts -->
<script>
function editAnnouncement(id, message) {
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_message").value = message;
    new bootstrap.Modal(document.getElementById("editModal")).show();
}

document.addEventListener("DOMContentLoaded", function () {
    const chartData = <?= json_encode($stats_data); ?>;
    new Chart(document.getElementById('statsChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: chartData.map(d => d.language),
            datasets: [{
                data: chartData.map(d => d.count),
                backgroundColor: ['#42a5f5', '#ef5350', '#ffb74d', '#66bb6a', '#ab47bc']
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } }
        }
    });
    document.getElementById("searchButton").addEventListener("click", function () {
    const query = document.getElementById("searchInput").value.trim();
    const resultDiv = document.getElementById("searchResults");

    if (!query) {
        resultDiv.innerHTML = "<p class='text-danger'>Please enter a query.</p>";
        return;
    }

    fetch("search.php?q=" + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                resultDiv.innerHTML = `<p class="text-danger">${data.error}</p>`;
            } else {
                resultDiv.innerHTML = `
    <div class="card border-success">
        <div class="card-body">
            <p><strong>ID:</strong> <span class="student-id">${data.student_id}</span></p>
            <p><strong>Name:</strong> <span class="student-name">${data.name}</span></p>
            <p><strong>Lab:</strong> <span class="student-lab">${data.lab}</span></p>

            <p><strong>Remaining Sessions:</strong> <span class="student-remaining">${data.remaining_sessions}</span></p>
            <button class="btn btn-success mt-2" id="openSitIn">✔ Create Sit-in Record</button>
        </div>
    </div>
`;

document.getElementById("openSitIn").addEventListener("click", () => {
    document.getElementById("sit_student_id").value = data.student_id;
    document.getElementById("sit_name").value = data.name;
    document.getElementById("sit_lab").value = data.lab;

    document.getElementById("sit_remaining").value = data.remaining_sessions;
    document.getElementById("sit_purpose").value = '';
    new bootstrap.Modal(document.getElementById("sitInModal")).show();
});

            }
        })
        .catch(err => {
            resultDiv.innerHTML = `<p class="text-danger">Error: ${err.message}</p>`;
        });
});
});


</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
