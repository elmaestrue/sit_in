<?php
session_start();
include 'db.php'; // Include database connection

// Fetch announcements
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY posted_at DESC");

// Handle new announcement submission
if (isset($_POST['submit_announcement'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn, "INSERT INTO announcements (title, message) VALUES ('Announcement', '$message')");
    header("Location: admin.php"); // Refresh page
    exit();
}

// Handle edit announcement
if (isset($_POST['edit_announcement'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $updateQuery = "UPDATE announcements SET message = '$message' WHERE id = $id";
    if (mysqli_query($conn, $updateQuery)) {
        header("Location: admin.php"); // Refresh page to show updates
        exit();
    } else {
        echo "Error updating announcement: " . mysqli_error($conn);
    }
}

// Fetch statistics
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM students"))['total'];
$current_sit_in = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM sit_in_records WHERE log_out_time IS NULL"))['total'];
$total_sit_in = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM sit_in_records"))['total'];

// Fetch pie chart data
$labels = [];
$values = [];
$query = mysqli_query($conn, "SELECT subject, COUNT(*) as count FROM sit_in_records GROUP BY subject");
while ($row = mysqli_fetch_assoc($query)) {
    $labels[] = $row['subject'];
    $values[] = $row['count'];
}

// Convert data to JSON format for Chart.js
$labels_json = json_encode($labels);
$values_json = json_encode($values);

// Function to export reports
if(isset($_GET['export'])) {
    $type = $_GET['export'];
    include 'export_report.php'; // Handle CSV, Excel, PDF exports
}

// Function to fetch statistics
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM students"))['total'];
//$total_sit_in = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM sit_in_records WHERE sit_out_time IS NULL"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
  <!-- Navbar -->
  <header class="navbar">
        <div class="nav">
            <h1>Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                 <!-- Search Modal -->
<div id="searchModal" class="sitmodal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 style="color: #6A0DAD">Search Student</h2>
        <input type="text" id="searchInput" placeholder="Enter Student Name or ID">
        <button id="searchBtn">Search</button>
        <div id="searchResults"></div> <!-- This is where the search result form will appear -->
    </div>
</div>
<li><a href="#" id="openSearchModal">Search</a></li>
                    
                    <li><a href="sitin.php">Sitin</a></li>
                    <li><a href="viewrecords.php">Sitin Records</a></li>
                    <li><a href="sitinreport.php">Sitin Reports</a></li>
                    <li><a href="#">Reservation</a></li>
                    <li><a href="login.php" class="logout-btn">Log out</a></li>
                </ul>
            </nav>
        </div>
    </header>



    <script src="script.js"></script>
<body>
<style> 

/* Center the modal */


.sitmodal {
    
    display: none;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
}

/* Bigger Modal Content */
.modal-content {
    background: white;
    width: 450px; /* Increased width */
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

/* Modal Header */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #ddd;
    padding-bottom: 15px;
}

.modal-title {
    color: #000;
    font-size: 22px; /* Bigger font */
    font-weight: bold;
}

.close-modal {
    background: none;
    border: none;
    font-size: 22px;
    cursor: pointer;
}

/* Bigger Form Fields */
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-weight: bold;
    display: block;
    color: #000;
    font-size: 16px; /* Bigger label */
}

.form-group input {
    width: 100%;
    padding: 8px;
    border: 2px solid #ccc;
    border-radius: 6px;
    font-size: 16px; /* Bigger text */
}

/* Bigger Buttons */
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 15px;
}

.close-modal {
    background: #ccc;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 6px;
    font-size: 16px;
}

.sit-in-btn {
    background: #0d6efd;
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 6px;
    font-size: 16px;
}

.sit-in-btn:hover {
    background: #0056b3;
}


  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: whitesmoke;
}

.nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

/* Navbar */
.navbar {
    background-color: #6A0DAD; 
    color: white;
    padding: 10px 20px;
    position: sticky; 
    top: 0;
    z-index: 1000;
}

.navbar h1 {
    margin: 0;
    font-size: 20px;
}

.navbar ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 15px;s
}

.navbar ul li a {
    text-decoration: none;
    color: white;
    padding: 8px 12px;
    transition: background-color 0.3s;  
}

.navbar ul li a:hover,
.navbar ul li a.logout-btn {
    border-radius: 4px;
}
body {
    background-color: #f4f4f4;
  
}

.container {
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

/* Statistics Section */
.statistics {
    background: white;
    padding: 20px;
    width: 30%;
    border-radius: 10px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    margin-left : 15%;
}

.statistics h3 {
    background: #007bff;
    color: white;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
}

canvas {
    margin-top: 20px;
    max-width: 100%;
}

/* Announcement Section */
.announcement {
    background: white;
    padding: 20px;
    width: 30%;
    border-radius: 10px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    margin-right : 30%;
}

.announcement h3 {
    background: #007bff;
    color: white;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
}

textarea {
    width: 100%;
    height: 50px;
    padding: 10px;
    margin-top: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    resize: none;
}

button {
    background: #28a745;
    color: white;
    padding: 10px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    margin-top: 10px;
}

button:hover {
    background: #218838;
}

.announcement-list {
    margin-top: 20px;
}

.announcement-list p {
    background: #f8f9fa;
    padding: 10px;
    border-left: 5px solid #007bff;
    margin-bottom: 10px;
    border-radius: 5px;
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
    }
    
    .statistics, .announcement {
        width: 100%;
    }
}

</style>

    <div class="container">
        <!-- Statistics Section -->
        <div class="statistics">
            <h3>📊 Statistics</h3>
            <p><strong>Students Registered:</strong> </p>
            <p><strong>Currently Sit-In:</strong></p>
            <p><strong>Total Sit-Ins:</strong> </p>
            <canvas id="pieChart"></canvas>
        </div>
        <div class="announcement">
    <h3>📢 Announcements</h3>
    <form method="POST">
        <textarea name="message" placeholder="New Announcement" required></textarea>
        <button type="submit" name="submit_announcement">Submit</button>
    </form>

    <h4>Posted Announcements</h4>
    <div class="announcement-list">
        <?php while ($row = mysqli_fetch_assoc($announcements)) { ?>
            <p>
                <strong><?php echo $row['posted_by']; ?> | <?php echo date("Y-M-d", strtotime($row['posted_at'])); ?></strong><br>
                <?php echo $row['message']; ?>
                <button type="button" class="edit-btn" data-id="<?php echo $row['id']; ?>" data-message="<?php echo htmlspecialchars($row['message'], ENT_QUOTES); ?>">Edit</button>
                
            </p>
        <?php } ?>
    </div>
</div>

<!-- Hidden Edit Form -->
<div id="editModal" class="sitmodal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Announcement</h2>
        <form method="POST">
            <input type="hidden" name="id" id="editId">
            <textarea name="message" id="editMessage" required></textarea>
            <button type="submit" name="edit_announcement">Save Changes</button>
        </form>
    </div>
</div>




    <script>
        
        document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("searchModal");
        const modalContent = document.querySelector("modal-content");
        const openBtn = document.getElementById("openSearchModal");
        const closeBtn = document.querySelector(".close");
        const searchBtn = document.getElementById("searchBtn");
        const searchInput = document.getElementById("searchInput");
        const searchResults = document.getElementById("searchResults");

        function openSearchModal() {
        modal.style.display = "flex"; // Display the modal
        modalContent.style.display = "flex"; // Display the modal

    }
        // Open Modal
        openBtn.addEventListener("click", function () {
            modal.style.display = "none";
            openSearchModal();
        });
        document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-btn");
    const editModal = document.getElementById("editModal");
    const editId = document.getElementById("editId");
    const editMessage = document.getElementById("editMessage");
    const closeEditModalBtn = document.querySelector("#editModal .close");

    // Open modal when clicking the Edit button
    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            const message = this.getAttribute("data-message");

            editId.value = id;
            editMessage.value = message;
            editModal.style.display = "flex"; // Show modal
        });
    });

    // Close modal when clicking the close button
    closeEditModalBtn.addEventListener("click", function () {
        editModal.style.display = "none";
    });

    // Close modal if clicking outside
    window.addEventListener("click", function (event) {
        if (event.target === editModal) {
            editModal.style.display = "none";
        }
    });
});


        // Close Modal
        closeBtn.addEventListener("click", function () {
            modal.style.display = "none";
        });

        // Close modal if clicked outside of it
        window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });

    // Search Functionality
    searchBtn.addEventListener("click", function () {
        const query = searchInput.value.trim();

        if (query === "") {
            alert("Please enter a search term.");
            return;
        }

        fetch("search.php?q=" + query)
            .then(response => response.text())
            .then(data => {
                searchResults.innerHTML = data;
                modal.style.display = "block"; // Show modal when data loads
            })
            .catch(error => console.error("Error fetching search results:", error));
    });

    // Handle dynamic modal button clicks (for "Sit In" and "Close")
    searchResults.addEventListener("click", function (event) {
        if (event.target.classList.contains("close-modal")) {
            modal.style.display = "none";
        } else if (event.target.classList.contains("sit-in-btn")) {
            alert("Sit-in recorded!"); 
            modal.style.display = "none";
        }
    });
});


        // Pie Chart Data
        const ctx = document.getElementById('pieChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['C++', 'Java', 'JavaScript', 'ASP.Net', 'PHP'],
                datasets: [{
                    data: [40, 25, 15, 10, ], 
                    backgroundColor: ['#FF6384', '#FF9F40', '#FFCD56', '#4BC0C0', '#36A2EB']
                }]
            }
          
        });
    </script>
</body>
</html>
