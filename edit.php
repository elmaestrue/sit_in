<?php
// Start session to access user data
session_start();

// Database connection (adjust with your actual connection settings)
$servername = "localhost"; // or your server
$username = "root"; // your username
$password = ""; // your password
$dbname = "test"; // your database name

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Example user details, replace with actual session or database values
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
$user_course = isset($_SESSION['user_course']) ? $_SESSION['user_course'] : '';
$user_year = isset($_SESSION['user_year']) ? $_SESSION['user_year'] : '';
$user_address = isset($_SESSION['user_address']) ? $_SESSION['user_address'] : '';
$user_session = isset($_SESSION['user_session']) ? $_SESSION['user_session'] : '';

// Check if the form was submitted to update the profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and process form data
    $user_name = htmlspecialchars($_POST['name']);
    $user_course = htmlspecialchars($_POST['course']);
    $user_year = htmlspecialchars($_POST['yearlevel']);
    $user_email = htmlspecialchars($_POST['email']);
    $user_address = htmlspecialchars($_POST['address']);
    $user_session = 30;

    // Update query to modify user profile details
    $user_id = $_SESSION['user_id']; // Assuming the user ID is stored in session

    $update_query = "UPDATE users SET name=?, course=?, year_level=?, email=?, address=?, session=? WHERE id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssi", $user_name, $user_course, $user_year, $user_email, $user_address, $user_session, $user_id);

    if ($stmt->execute()) {
        // If successful, update the session
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_email'] = $user_email;
        $_SESSION['user_course'] = $user_course;
        $_SESSION['user_year'] = $user_year;
        $_SESSION['user_address'] = $user_address;
        $_SESSION['user_session'] = $user_session;
        echo "<div class='alert alert-success'>Profile updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating profile!</div>";
    }

    // Profile picture update (if uploaded)
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $upload_dir = "uploads/";
        $file_name = basename($_FILES['profile_image']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            // Update profile picture in the database
            $update_image_query = "UPDATE users SET profile_image=? WHERE id=?";
            $stmt = $conn->prepare($update_image_query);
            $stmt->bind_param("si", $target_file, $user_id);
            $stmt->execute();

            // Update session variable with the new profile picture
            $_SESSION['profile_image'] = $target_file;
        }
    }

    // Redirect back to the profile page (or show a success message)
    header("Location: edit.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f8f9fa;
            padding-top: 70px;
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

        /* Form Container */
        .container {
            max-width: 600px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .profile-img {
            display: block;
            margin: 0 auto 15px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0D47A1;
        }

        .btn-primary {
            background-color: #0D47A1;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0B3A85;
        }

        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <header>
        <div class="logosec">
            <div class="logo">Dashboard</div>
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210182541/Untitled-design-(30).png"
                class="icn menuicn" alt="menu-icon">
        </div>

        <div class="header-dashboard">
            <h2>Edit Profile</h2>
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
                    <h3 class="nav-text">Home</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="edit.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Profile</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="edit.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Edit</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="index.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">View Announcement</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="index.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Sit-in Rules</h3>
                </a>
            </div>
            <div class="nav-option">
                <a href="index.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Lab Rules & Regulations</h3>
                </a>
            </div>
            <div class="nav-option"><h3 class="nav-text">Sit-in History</h3></div>
            <div class="nav-option"><h3 class="nav-text">Reservation</h3></div>
            <div class="nav-option"><h3 class="nav-text">View Remaining Session</h3></div>
            <div class="nav-option">
                <a href="logout.php" style="color: white; text-decoration: none;">
                    <h3 class="nav-text">Log-Out</h3>
                </a>
            </div>
        </div>
    </nav>
</div>

<!-- CSS -->
<style>
    .nav-text {
        font-size: 14px; /* Adjust the size as needed */
    }
</style>


    <!-- Profile Section -->
    <div class="container">
        <h3 class="text-center mb-4">Your Profile</h3>

        <!-- Profile Image -->
        <div class="text-center">
            <img src="<?php echo isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'lob.jpg'; ?>"
                alt="Profile Image" class="profile-img" id="profilePreview">
        </div>

        <!-- Profile Info -->
        <form action="edit.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name"><b>Name:</b></label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $user_name; ?>"
                    placeholder="Enter your name">
            </div>

            <div class="form-group">
                <label for="course"><b>Course:</b></label>
                <select name="course" required>
                    <option value="BSIT" <?php if ($user_course == 'BSIT') echo 'selected'; ?>>BSIT</option>
                    <option value="BSCS" <?php if ($user_course == 'BSCS') echo 'selected'; ?>>BSCS</option>
                    <option value="BSA" <?php if ($user_course == 'BSA') echo 'selected'; ?>>BSA</option>
                    <option value="BSBA" <?php if ($user_course == 'BSBA') echo 'selected'; ?>>BSBA</option>
                    <option value="BSEE" <?php if ($user_course == 'BSEE') echo 'selected'; ?>>BSEE</option>
                    <option value="BSECE" <?php if ($user_course == 'BSECE') echo 'selected'; ?>>BSECE</option>
                </select>
            </div>

            <div class="form-group">
                <label for="year"><b>Year Level:</b></label>
                <select name="yearlevel" required>
                    <option value="1" <?php if ($user_year == '1') echo 'selected'; ?>>1st Year</option>
                    <option value="2" <?php if ($user_year == '2') echo 'selected'; ?>>2nd Year</option>
                    <option value="3" <?php if ($user_year == '3') echo 'selected'; ?>>3rd Year</option>
                    <option value="4" <?php if ($user_year == '4') echo 'selected'; ?>>4th Year</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email"><b>Email:</b></label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $user_email; ?>"
                    placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="address"><b>Address:</b></label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo $user_address; ?>"
                    placeholder="Enter your address">
            </div>


            <!-- Profile Image Upload -->
            <div class="form-group">
                <label for="profile_image"><b>Upload New Profile Picture:</b></label>
                <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
            </div>

            <!-- Save & Back Buttons -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>

    <!-- Sidebar Toggle Script -->
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

        // Profile Image Preview
        document.getElementById("profile_image").addEventListener("change", function(event) {
            let reader = new FileReader();
            reader.onload = function() {
                let output = document.getElementById("profilePreview");
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>

</body>

</html>
