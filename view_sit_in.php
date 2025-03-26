<?php
include 'db.php'; // Ensure db connection is included

// Fetch students currently sitting-in
$query = "SELECT student_id, sit_in_time FROM sit_in_records ORDER BY sit_in_time DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currently Sitting-in Students</title>
    <style>
        table {
            width: 60%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 10px;
            border: 1px solid black;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 5px 10px;
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

    <h2>Currently Sitting-in Students</h2>
    <table>
        <tr>
            <th>Student ID</th>
            <th>Sit-in Time</th>
            <th>Action</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['student_id']}</td>
                        <td>{$row['sit_in_time']}</td>
                        <td><a href='reset_session.php?student_id={$row['student_id']}'><button>Reset Session</button></a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No students currently sitting-in.</td></tr>";
        }
        ?>
    </table>

</body>
</html>

<?php $conn->close(); ?>

<!-- Sit-In Form Modal -->
<div id="sitInModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sit In Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="sitInForm">
                    <div class="form-group">
                        <label>ID Number:</label>
                        <input type="text" class="form-control" id="student_id" readonly>
                    </div>
                    <div class="form-group">
                        <label>Student Name:</label>
                        <input type="text" class="form-control" id="student_name" readonly>
                    </div>
                    <div class="form-group">
                        <label>Purpose:</label>
                        <input type="text" class="form-control" id="purpose">
                    </div>
                    <div class="form-group">
                        <label>Lab:</label>
                        <input type="text" class="form-control" id="lab">
                    </div>
                    <div class="form-group">
                        <label>Remaining Session:</label>
                        <input type="number" class="form-control" id="remaining_session" readonly>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Sit In</button>
                </form>
            </div>
        </div>
    </div>
</div>


