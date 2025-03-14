<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.html");
    exit();
}

// Assuming user details like name, email, etc., are stored in the session
$user_name = $_SESSION['username']; // example of username from session
$user_email = $_SESSION['email']; // email example, you can set others as needed
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Basic Styling */
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #0D47A1;
            color: white;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .logosec {
            display: flex;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-right: 10px;
        }

        .icn.menuicn {
            width: 30px;
            cursor: pointer;
        }

        .header-dashboard h2 {
            font-size: 24px;
            margin: 0;
        }

        /* Sidebar Navigation */
        .navcontainer {
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            background-color: #2c3e50;
            transition: left 0.3s ease-in-out;
            z-index: 1100;
            padding-top: 20px;
        }

        .navcontainer.open {
            left: 0;
        }

        .nav {
            display: flex;
            flex-direction: column;
        }

        .nav-option {
            padding: 15px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .nav-option:hover {
            background-color: #34495e;
            transform: translateX(10px);
        }

        /* Sidebar Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 1050;
        }

        .overlay.show {
            display: block;
        }

        /* Main Content */
        .main-content {
            padding-top: 70px;
        }

        /* Sidebar Student Info */
        .sidebar {
            background-color: #0D47A1;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .sidebar img {
            width: 100px;
            border-radius: 50%;
            display: block;
            margin: auto;
        }

        .card {
            border-radius: 5px;
        }

        .card-header {
            background-color: #0D47A1;
            color: white;
            font-weight: bold;
        }

        .rules-content {
            max-height: 250px;
            overflow-y: auto;
            padding: 10px;
            background-color: #ffffff;
        }

        /* Responsive Design */
        @media screen and (max-width: 850px) {
            .navcontainer {
                width: 100vw;
                left: -100vw;
            }

            .navcontainer.open {
                left: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <header>
        <div class="logosec">
            <div class="logo">Dashboard</div>
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210182541/Untitled-design-(30).png" class="icn menuicn" alt="menu-icon">
        </div>
    </header>

    <!-- Sidebar Overlay -->
    <div class="overlay"></div>

    <!-- Sidebar Navigation -->
    <div class="navcontainer">
        <nav class="nav">
            <div class="nav-upper-options">
                <div class="nav-option">
                    <a href="index.php" style="color: white; text-decoration: none;">
                        <h3>Home</h3>
                    </a>
                </div>
                <div class="nav-option">
                    <a href="edit.php" style="color: white; text-decoration: none;">
                        <h3>Profile</h3>
                    </a>
                </div>
                <div class="nav-option">
                    <a href="edit.php" style="color: white; text-decoration: none;">
                        <h3>Edit</h3>
                    </a>
                </div>
                <div class="nav-option">
                    <a href="index.php" style="color: white; text-decoration: none;">
                        <h3>View Announcement</h3>
                    </a>
                </div>
                <div class="nav-option">
                    <a href="index.php" style="color: white; text-decoration: none;">
                        <h3>Sit-in Rules</h3>
                    </a>
                </div>
                <div class="nav-option">
                    <a href="index.php" style="color: white; text-decoration: none;">
                        <h3>Lab Rules & Regulations</h3>
                    </a>
                </div>
                <div class="nav-option"><h3>Sit-in History</h3></div>
                <div class="nav-option"><h3>Reservation</h3></div>
                <div class="nav-option"><h3>View Remaining Session</h3></div>
                <div class="nav-option">
                    <a href="logout.php" style="color: white; text-decoration: none;">
                        <h3>Log-Out</h3>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Main Content Section -->
    <div class="container mt-5 main-content">
        <div class="row">
            <!-- Sidebar (Student Info) -->
            <div class="col-md-3">
    <div class="sidebar text-center p-3">
        <h5 class="mb-3">Student Information</h5>
        <!-- Displaying the user profile picture -->
        <img src="<?php echo isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'lob.jpg'; ?>" 
             alt="Student Profile" class="rounded-circle mb-3" style="width: 100px; height: 100px;">
        <hr>
        <p><strong>👤 Name:</strong> <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?></p>
        <p><strong>🎓 Course:</strong> <?php echo isset($_SESSION['user_course']) ? htmlspecialchars($_SESSION['user_course']) : ''; ?></p>
        <p><strong>📅 Year:</strong> <?php echo isset($_SESSION['user_year']) ? htmlspecialchars($_SESSION['user_year']) : ''; ?></p>
        <p><strong>📧 Email:</strong> <?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?></p>
        <p><strong>🏠 Address:</strong> <?php echo isset($_SESSION['user_address']) ? htmlspecialchars($_SESSION['user_address']) : ''; ?></p>
        <p><strong>⏳ Session:</strong> <?php echo isset($_SESSION['user_session']) ? htmlspecialchars($_SESSION['user_session']) : '30'; ?></p>
    </div>
</div>

            <!-- Announcement Section -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">📢 Announcement</div>
                    <div class="card-body">
                        <p><strong>CCS Admin | 2025-Feb-03</strong></p>
                        <p>The College of Computer Studies will open the registration of students for the Sit-in privilege starting tomorrow. Thank you! Lab Supervisor</p>
                    </div>
                    <div class="card-body">
                        <p><strong>CCS Admin | 2024-May-08</strong></p>
                        <p>Important Announcement We are excited to announce the launch of our new website! 🎉 Explore our latest products and services now!</p>
                    </div>
                </div>
            </div>


            <!-- Rules and Regulations -->
            <div class="col-md-4">
    <div class="card">
        <div class="card-header">📜 Rules and Regulation</div>
        <div class="card-body rules-content">
            <h6><strong>University of Cebu</strong></h6>
            <h6><strong>COLLEGE OF INFORMATION & COMPUTER STUDIES</strong></h6>
            <h6><strong>LABORATORY RULES AND REGULATIONS</strong></h6>
            
            <p>To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
            <ul>
                <li><strong>1.</strong> Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans, and other personal equipment must be switched off.</li>
                <li><strong>2.</strong> Games are not allowed inside the lab. This includes computer-related games, card games, and other games that may disturb the operation of the lab.</li>
                <li><strong>3.</strong> Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing software are strictly prohibited.</li>
                <li><strong>4.</strong> Accessing websites not related to the course (especially pornographic and illicit sites) is strictly prohibited.</li>
                <li><strong>5.</strong> Deleting computer files and changing the computer setup is a major offense.</li>
                <li><strong>6.</strong> Observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".</li>
                <li><strong>7.</strong> Observe proper decorum while inside the laboratory:
                    <ul>
                        <li>Do not enter the lab unless the instructor is present.</li>
                        <li>All bags, knapsacks, and similar items must be deposited at the counter.</li>
                        <li>Follow the seating arrangement of your instructor.</li>
                        <li>At the end of class, close all software programs.</li>
                        <li>Return all chairs to their proper places after using.</li>
                    </ul>
                </li>
                <li><strong>8.</strong> Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.</li>
                <li><strong>9.</strong> Anyone causing a continual disturbance will be asked to leave the lab. Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</li>
                <li><strong>10.</strong> Persons exhibiting hostile or threatening behavior such as yelling, swearing, or disregarding requests made by lab personnel will be asked to leave the lab.</li>
                <li><strong>11.</strong> For serious offenses, lab personnel may call the Civil Security Office (CSU) for assistance.</li>
                <li><strong>12.</strong> Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant, or instructor immediately.</li>
            </ul>
            <hr>
            <h6>Disciplinary Action</h6>
            <ul>
                <li><strong>First Offense:</strong> The Head, Dean, or OIC recommends the Guidance Center for a suspension from classes for each offender.</li>
                <li><strong>Second and Subsequent Offenses:</strong> A recommendation for a heavier sanction will be endorsed to the Guidance Center.</li>
            </ul>
        </div>
    </div>
</div>

    <!-- Script to handle sidebar toggle -->
    <script>
        let menuIcon = document.querySelector(".menuicn");
        let sidebar = document.querySelector(".navcontainer");
        let overlay = document.querySelector(".overlay");

        menuIcon.addEventListener("click", () => {
            sidebar.classList.add("open");
            overlay.classList.add("show");
        });

        overlay.addEventListener("click", () => {
            sidebar.classList.remove("open");
            overlay.classList.remove("show");
        });
    </script>
</body>

</html>
