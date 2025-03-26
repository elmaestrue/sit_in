<?php
session_start(); // Start the session at the beginning of the script

// Database connection details
$servername = "localhost";  // XAMPP server
$db_username = "root";      // Default MySQL username in XAMPP
$db_password = "";          // Default MySQL password in XAMPP (empty)
$dbname = "test";  // Replace with the name of your database

// Create a connection to the MySQL database
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data from the registration form
$idno = $_POST['idno'];
$lastname = $_POST['lname'];  // Correct field names from the form
$firstname = $_POST['fname'];  // Correct field names from the form
$middlename = $_POST['mname'];  // Correct field names from the form
$course = $_POST['course'];
$yearlevel = $_POST['yearlevel'];
$email = $_POST['email'];  // Assuming there's an email field
$username = $_POST['username'];
$password = $_POST['password']; // This is the password entered by the user
$address = $_POST['address']; // Added address field

// Hash the password before storing (for security)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare SQL statement to insert data into the database
$stmt = $conn->prepare("INSERT INTO studentinfo (idno, lastname, firstname, middlename, course, email, yearlevel, username, password, address) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Bind the parameters to the prepared statement (data types: s = string)
$stmt->bind_param("ssssssssss", $idno, $lastname, $firstname, $middlename, $course, $email, $yearlevel, $username, $hashed_password, $address);

// Execute the statement and check for success
if ($stmt->execute()) {
    // Store session data
    $_SESSION['user_id'] = $idno;
    $_SESSION['user_name'] = $username;
    $_SESSION['user_email'] = $email; // Add email session
    $_SESSION['user_course'] = $course;
    $_SESSION['user_year'] = $yearlevel;
    $_SESSION['user_address'] = $address; // Store address in session
    $_SESSION['user_session'] = 30; // Add session info if needed

    // Redirect to login page after successful registration
    echo "<script type='text/javascript'>alert('Registration successful!');</script>";
    echo "<script>window.location.href = 'login.html';</script>"; // Redirect to the login page after success
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and the database connection
$stmt->close();
$conn->close();
?>
