<?php
session_start();  // Start the session

// Check if the user is logged in by checking for 'user_id' in the session
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Assuming you want to fetch and update user details here
include('db.php');  // Include database connection

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);  // Use integer for user_id in the query
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();  // Fetch user details into $user array
    $user_name = $user['name'];
    $user_email = $user['email'];
    $user_course = $user['course'];
    $user_year = $user['yearlevel'];
    $user_address = $user['address'];
    $user_session = $user['session'];
} else {
    // Handle case where no user is found (shouldn't happen if logged in)
    echo "User not found!";
    exit();
}

// Now handle the form submission and update logic below
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated data from the form
    $name = $_POST['name'];
    $course = $_POST['course'];
    $yearlevel = $_POST['yearlevel'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $session = $_POST['session'];

    // Prepare SQL update query
    $sql = "UPDATE users SET name=?, course=?, yearlevel=?, email=?, address=?, session=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissii", $name, $course, $yearlevel, $email, $address, $session, $user_id);

    if ($stmt->execute()) {
        // Redirect to the profile page or show success message
        header("Location: edit.php?success=1");
        exit();
    } else {
        echo "Error updating profile: " . $stmt->error;
    }
}
?>
