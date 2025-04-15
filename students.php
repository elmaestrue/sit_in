<?php
session_start();
include 'db.php';

// Handle Reset All Sessions
if (isset($_POST['reset_all'])) {
    $reset = "UPDATE students SET remaining_sessions = 30";
    mysqli_query($conn, $reset);
    header("Location: students.php");
    exit();
}

// Handle Add Student
if (isset($_POST['add_student'])) {
    $id = $_POST['student_id'];
    $name = $_POST['name'];
    $year = $_POST['year'];
    $course = $_POST['course'];
    $insert = "INSERT INTO students (student_id, name, year, course, remaining_sessions) VALUES ('$id', '$name', '$year', '$course', 30)";
    mysqli_query($conn, $insert);
    header("Location: students.php");
    exit();
}

// Handle Edit Student
if (isset($_POST['edit_student'])) {
    $id = $_POST['student_id'];
    $name = $_POST['edit_name'];
    $year = $_POST['edit_year'];
    $course = $_POST['edit_course'];
    $reset = isset($_POST['reset_sessions']) ? ", remaining_sessions = 30" : "";
    $update = "UPDATE students SET name='$name', year='$year', course='$course' $reset WHERE student_id='$id'";
    mysqli_query($conn, $update);
    header("Location: students.php");
    exit();
}

// Fetch all students
$sql = "SELECT * FROM students ORDER BY student_id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d47a1;">
    <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold">College of Computer Studies Admin</a>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link text-white" href="admin.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#searchModal">Search</a></li>
                <li class="nav-item"><a class="nav-link text-white active" href="#">Students</a></li>
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
    <h1 class="text-center mb-4">Students Information</h1>
    <div class="d-flex justify-content-start mb-3">
        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addModal">Add Students</button>
        <form method="POST" onsubmit="return confirm('Are you sure you want to reset all sessions to 30?');">
            <button type="submit" name="reset_all" class="btn btn-danger">Reset All Session</button>
        </form>
    </div>

    <table id="studentTable" class="table table-striped table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Year Level</th>
                <th>Course</th>
                <th>Remaining Session</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['student_id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['year'] ?></td>
                <td><?= $row['course'] ?></td>
                <td><?= $row['remaining_sessions'] ?></td>
                <td>
                    <button class="btn btn-sm btn-primary editBtn"
                        data-id="<?= $row['student_id'] ?>"
                        data-name="<?= $row['name'] ?>"
                        data-year="<?= $row['year'] ?>"
                        data-course="<?= $row['course'] ?>">
                        Edit
                    </button>
                    <a href="delete_student.php?student_id=<?= $row['student_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this student?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="student_id" class="form-control mb-2" placeholder="ID Number" required>
        <input type="text" name="name" class="form-control mb-2" placeholder="Full Name" required>
        <input type="text" name="year" class="form-control mb-2" placeholder="Year Level" required>
        <input type="text" name="course" class="form-control mb-2" placeholder="Course" required>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_student" class="btn btn-success">Add Student</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="student_id" id="edit_id">
        <input type="text" name="edit_name" id="edit_name" class="form-control mb-2" placeholder="Full Name">
        <input type="text" name="edit_year" id="edit_year" class="form-control mb-2" placeholder="Year Level">
        <input type="text" name="edit_course" id="edit_course" class="form-control mb-2" placeholder="Course">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="reset_sessions" id="reset_sessions" checked>
          <label class="form-check-label" for="reset_sessions">
            Reset Remaining Sessions to 30
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="edit_student" class="btn btn-success">Save Changes</button>
      </div>
    </form>
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

<!-- Sit In Modal -->
<div class="modal fade" id="sitInModal" tabindex="-1">
  <div class="modal-dialog">
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    $('#studentTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "columnDefs": [{ orderable: false, targets: 5 }]
    });

    $('.editBtn').on('click', function () {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_year').val($(this).data('year'));
        $('#edit_course').val($(this).data('course'));
        $('#editModal').modal('show');
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
                resultDiv.html(`<p class="text-danger">Error: ${err.message}</p>`);
            });
    });
});
</script>

</body>
</html>
