<?php
// Start the session to manage session data
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Sample session time remaining (this can be dynamic as per your system's logic)
$remainingSessionTime = 30; // Example session time
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-In Monitoring Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="responsive.css">
</head>

<body>
    <!-- Header Section -->
    <header>
        <div class="logosec">
            <div class="logo">CCS Sit-In</div>
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210182541/Untitled-design-(30).png" class="icn menuicn" id="menuicn" alt="menu-icon">
        </div>

        <div class="searchbar">
            <input type="text" placeholder="Search">
            <div class="searchbtn">
                <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180758/Untitled-design-(28).png" class="icn srchicn" alt="search-icon">
            </div>
        </div>

        <div class="message">
            <div class="circle"></div>
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210183322/8.png" class="icn" alt="">
            <div class="dp">
                <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180014/profile-removebg-preview.png" class="dpicn" alt="dp">
            </div>
        </div>
    </header>

    <div class="main-container">
        <!-- Navigation Menu -->
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <div class="nav-option option1">
                        <a href="dashboard.php">
                            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210182148/Untitled-design-(29).png" class="nav-img" alt="dashboard">
                            <h3>Dashboard</h3>
                        </a>
                    </div>
                    <div class="nav-option option5">
                        <a href="profile.php">
                            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210183323/10.png" class="nav-img" alt="profile">
                            <h3>Profile</h3>
                        </a>
                    </div>
                    <div class="nav-option option6">
                        <a href="settings.php">
                            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210183320/4.png" class="nav-img" alt="settings">
                            <h3>Settings</h3>
                        </a>
                    </div>
                    <div class="nav-option logout">
                        <a href="logout.php">
                            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210183321/7.png" class="nav-img" alt="logout">
                            <h3>Logout</h3>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="main">
            <!-- Dashboard Content -->
            <div class="searchbar2">
                <input type="text" placeholder="Search">
                <div class="searchbtn">
                    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180758/Untitled-design-(28).png" class="icn srchicn" alt="search-button">
                </div>
            </div>

            <div class="box-container">
                <div class="box box1">
                    <div class="text">
                        <h2 class="topic-heading">60.5k</h2>
                        <h2 class="topic">Article Views</h2>
                    </div>
                    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210184645/Untitled-design-(31).png" alt="Views">
                </div>

                <div class="box box2">
                    <div class="text">
                        <h2 class="topic-heading">150</h2>
                        <h2 class="topic">Likes</h2>
                    </div>
                    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210185030/14.png" alt="likes">
                </div>

                <div class="box box3">
                    <div class="text">
                        <h2 class="topic-heading">320</h2>
                        <h2 class="topic">Comments</h2>
                    </div>
                    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210184645/Untitled-design-(32).png" alt="comments">
                </div>

                <div class="box box4">
                    <div class="text">
                        <h2 class="topic-heading">70</h2>
                        <h2 class="topic">Published</h2>
                    </div>
                    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210185029/13.png" alt="published">
                </div>
            </div>

            <div class="report-container">
                <div class="report-header">
                    <h1 class="recent-Articles">Recent Announcements</h1>
                    <button class="view">View All</button>
                </div>

                <div class="report-body">
                    <div class="report-topic-heading">
                        <h3 class="t-op">Announcement</h3>
                        <h3 class="t-op">Views</h3>
                        <h3 class="t-op">Comments</h3>
                        <h3 class="t-op">Status</h3>
                    </div>

                    <div class="items">
                        <!-- Example of announcement items -->
                        <div class="item1">
                            <h3 class="t-op-nextlvl">Announcement 1</h3>
                            <h3 class="t-op-nextlvl">2.9k</h3>
                            <h3 class="t-op-nextlvl">210</h3>
                            <h3 class="t-op-nextlvl label-tag">Published</h3>
                        </div>

                        <div class="item1">
                            <h3 class="t-op-nextlvl">Announcement 2</h3>
                            <h3 class="t-op-nextlvl">1.5k</h3>
                            <h3 class="t-op-nextlvl">360</h3>
                            <h3 class="t-op-nextlvl label-tag">Published</h3>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- End of main container -->
    </div>

    <script src="./index.js"></script>
</body>

</html>
