<?php
// Start the session
session_start();
$_SESSION['user_id'] = $user_id; // Where $user_id is the ID of the logged-in user


// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if 'uname' and 'psw' are set in the POST data
    if (isset($_POST['uname']) && isset($_POST['psw'])) {
        // Database connection details
        $servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $dbname = "test"; // Change to your actual database name

        // Create a connection to the database
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Retrieve data from login form
        $username = $_POST['uname'];
        $password = $_POST['psw'];

        // Prepare SQL statement to check the username
        $sql = "SELECT * FROM studentinfo WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            // SQL preparation failed
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User found, now verify the password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password matches, set session variable and redirect to index
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
                exit();
            } else {
                // Password does not match
                echo "Incorrect password!";
            }
        } else {
            // Username not found
            echo "Username not found!";
        }

        // Close the connection
        $stmt->close();
        $conn->close();
    } else {
        // If 'uname' or 'psw' is not set, display a message
        echo "Please enter both username and password!";
    }
} else {
    // Not a POST request, display an error message
    echo "Invalid request!";
}
?>
