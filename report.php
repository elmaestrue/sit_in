<?php
include('db.php'); // Make sure this file exists and contains the $conn connection

// Handle filters
$filter_date = $_GET['date'] ?? '';
$filter_lab = $_GET['lab'] ?? '';

$query = "SELECT * FROM sit_in_records WHERE 1";

if (!empty($filter_date)) {
    $query .= " AND DATE(date) = '" . mysqli_real_escape_string($conn, $filter_date) . "'";
}
if (!empty($filter_lab)) {
    $query .= " AND laboratory = '" . mysqli_real_escape_string($conn, $filter_lab) . "'";
}

$query .= " ORDER BY date DESC, time_in DESC";
$result = mysqli_query($conn, $query);

// Fetch unique labs for filter dropdown
$labs = mysqli_query($conn, "SELECT DISTINCT laboratory FROM sit_in_records");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
</head>
<body>

<!-- Updated Navbar to match admin.php -->
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
    <h2 class="text-center mb-4">Generate Reports</h2>

    <form class="row g-2 mb-3" method="GET">
        <div class="col-md-3">
            <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <select name="lab" class="form-select">
                <option value="">All Laboratories</option>
                <?php while ($lab = mysqli_fetch_assoc($labs)): ?>
                    <option value="<?= $lab['laboratory'] ?>" <?= ($filter_lab == $lab['laboratory']) ? 'selected' : '' ?>>
                        <?= $lab['laboratory'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary" type="submit">Search</button>
            <a href="report.php" class="btn btn-danger">Reset</a>
        </div>
    </form>

    <table id="reportTable" class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Purpose</th>
                <th>Laboratory</th>
                <th>Login</th>
                <th>Logout</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                    <td><?= htmlspecialchars($row['laboratory']) ?></td>
                    <td><?= htmlspecialchars(date("h:i:sa", strtotime($row['time_in']))) ?></td>
                    <td><?= $row['time_out'] ? htmlspecialchars(date("h:i:sa", strtotime($row['time_out']))) : '-' ?></td>
                    <td><?= htmlspecialchars(date("Y-m-d", strtotime($row['date']))) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTable Export Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>

<script>
    $(document).ready(function () {
        $('#reportTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['csv', 'excel', 'pdf', 'print']
        });
    });
</script>

</body>
</html>
