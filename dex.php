<?php
session_start();
include("db.php"); // Include database connection

$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY posted_at DESC LIMIT 5");


// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from the database
$username = $_SESSION['username'];
$sql = "SELECT * FROM registration WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update session with the latest values
$_SESSION['firstname'] = $user['firstname'];
$_SESSION['lastname'] = $user['lastname'];
$_SESSION['email'] = $user['email'];
$_SESSION['course'] = $user['course'];
$_SESSION['yearlevel'] = $user['yearlevel'];
$_SESSION['address'] = $user['address'];
?>
<style> 
       /* General Reset */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: whitesmoke;
}

.container {
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

/* Main Dashboard Container */
.dashboard-container {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
    padding: 20px;
    max-width: 1200px;
    margin: 20px auto;
}

/* Cards */
.card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 15px;
    margin-top: 40px;

}

.card h2 {
    background-color:#6A0DAD;
    position: sticky;
    color: white;
    padding: 10px;
    margin: -15px -15px 15px -15px;
    text-align: center;
    border-radius: 8px 8px 0 0;
}

/* Student Information */
.student-info .profile {
    text-align: center;
}

.student-info img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin-bottom: 10px;
}

.student-info p {
    margin: 5px 0;
    font-size: 14px;
}

/* Announcements */
.announcements .announcement {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

.announcements .announcement:last-child {
    border-bottom: none;
}

/* Rules */
.rules {
    overflow-y: auto;
    max-height: 300px;
}

.rules h3 {
    margin-top: 15px;
}

    </style>
    <!-- Navbar -->
    <header class="navbar">
        <div class="container">
            <h1>Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="#">Notification</a></li>
                    <li><a href="#">Home</a></li>
                    <li><a href="editprofile.php">Edit Profile</a></li>
                    <li><a href="#">History</a></li>
                    <li><a href="#">Reservation</a></li>
                    <li><a href="login.php" class="logout-btn">Log out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="dashboard-container">
    <!-- Student Information -->
    <section class="card student-info">
        <h2>Student Information</h2>
        <div class="profile">
            <img src="barou.jpg" alt="Profile Picture">
            <p><strong>Name:</strong> <?php echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></p>
            <p><strong>Course:</strong> <?php echo $_SESSION['course']; ?></p>
            <p><strong>Year:</strong> <?php echo $_SESSION['yearlevel']; ?></p>
            <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
            <p><strong>Address:</strong> <?php echo $_SESSION['address']; ?></p>
        </div>
    </section>

    <!-- Announcements -->
    <section class="card announcements">
        <h2>Announcements</h2>
        <?php while ($row = mysqli_fetch_assoc($announcements)) { ?>
            <div class="announcement">
                <p><strong><?php echo $row['posted_by']; ?> | <?php echo date("Y-M-d", strtotime($row['posted_at'])); ?></strong></p>
                <p><?php echo $row['message']; ?></p>
            </div>
        <?php } ?>
    </section>

    <!-- Rules and Regulations -->
    <section class="card rules">
        <h2>Rules and Regulation</h2>
        <h4><p class="mb-2 text-center"><strong>University of Cebu COLLEGE OF INFORMATION & COMPUTER STUDIES</strong></p></h4>
        <p><strong>LABORATORY RULES AND REGULATIONS</strong></p>
        <p>To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
        <p>1. Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans and other personal pieces of equipment must be switched off.</p>
        <p>2. Games are not allowed inside the lab. This includes computer-related games, card games and other games that may disturb the operation of the lab.</p>
        <p>3. Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing of software are strictly prohibited.</p>
        <p>4. Getting access to other websites not related to the course (especially pornographic and illicit sites) is strictly prohibited.</p>
        <p>5. Deleting computer files and changing the set-up of the computer is a major offense.</p>
        <p>6. Observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".</p>
        <p>7. Observe proper decorum while inside the laboratory.</p>
        <li>Do not get inside the lab unless the instructor is present.</li>
        <li>All bags, knapsacks, and the likes must be deposited at the counter.</li>
        <li>Follow the seating arrangement of your instructor.</li>
        <li>At the end of class, all software programs must be closed.</li>
        <li>Return all chairs to their proper places after using.</li>
        <p>8. Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.</p>
        <p>9. Anyone causing a continual disturbance will be asked to leave the lab. Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</p>
        <p>10. Persons exhibiting hostile or threatening behavior such as yelling, swearing, or disregarding requests made by lab personnel will be asked to leave the lab.</p>
        <p>11. For serious offenses, the lab personnel may call the Civil Security Office (CSU) for assistance.</p>
        <p>12. Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant, or instructor immediately.</p>
        <h4>Disciplinary Action</h4>
        <p><strong>First Offense:</strong> The Head or the Dean or OIC recommends to the Guidance Center for a suspension from classes for each offender.</p>
        <p><strong>Second and Subsequent Offenses:</strong> A recommendation for a heavier sanction will be endorsed to the Guidance Center.</p>
    </section>
</main>

</body>
</html>