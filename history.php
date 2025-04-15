<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle feedback submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_feedback'])) {
    $student_id = $_POST['student_id'];
    $sit_in_id = $_POST['sit_in_id'];
    $message = trim($_POST['message']);
    $date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO feedback (sit_in_id, student_id, message, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $sit_in_id, $student_id, $message, $date);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Feedback submitted successfully.";
    } else {
        $_SESSION['error'] = "Failed to submit feedback.";
    }

    $stmt->close();
    header("Location: history.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>History Information</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .btn-feedback {
            background-color: #28a745;
            color: white;
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
                <li class="nav-item"><a class="nav-link text-white" href="#" data-toggle="modal" data-target="#searchModal">Search</a></li>
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

<div class="container mt-4">
    <h3 class="text-center mb-4">Sit-In History</h3>

    <!-- Alerts -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <table id="historyTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Sit Purpose</th>
                <th>Laboratory</th>
                <th>Login</th>
                <th>Logout</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM sit_in_records WHERE log_out_time IS NOT NULL ORDER BY date DESC";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $student_id = $row['student_id'];
            $name = $row['name'];
            $purpose = $row['purpose'];
            $lab = $row['laboratory'];
            $time_in = $row['time_in'];
            $time_out = $row['log_out_time'];
            $date = $row['date'];
            $sit_in_id = $row['id'];

            echo "<tr>
                    <td>$student_id</td>
                    <td>$name</td>
                    <td>$purpose</td>
                    <td>$lab</td>
                    <td>$time_in</td>
                    <td>$time_out</td>
                    <td>" . date("Y-m-d", strtotime($date)) . "</td>
                    <td>
                        <button 
                            class='btn btn-feedback btn-sm openFeedbackModal' 
                            data-student-id='$student_id' 
                            data-name='$name' 
                            data-laboratory='$lab' 
                            data-sit-in-id='$sit_in_id'>
                            Give Feedback
                        </button>
                    </td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Feedback</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="student_id" id="feedbackStudentId">
                <input type="hidden" name="sit_in_id" id="feedbackSitInId">
                <input type="hidden" name="submit_feedback" value="1">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="feedbackName" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Laboratory</label>
                    <input type="text" id="feedbackLab" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="4" placeholder="Enter your feedback here..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit Feedback</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    $('#historyTable').DataTable();

    $('.openFeedbackModal').click(function () {
        const studentId = $(this).data('student-id');
        const name = $(this).data('name');
        const lab = $(this).data('laboratory');
        const sitInId = $(this).data('sit-in-id');

        $('#feedbackStudentId').val(studentId);
        $('#feedbackName').val(name);
        $('#feedbackLab').val(lab);
        $('#feedbackSitInId').val(sitInId);

        $('#feedbackModal').modal('show');
    });
});
</script>

</body>
</html>
