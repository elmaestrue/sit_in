<?php
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['uname'], $_POST['psw'])) {
        $conn = new mysqli("localhost", "root", "", "test");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $username = $_POST['uname'];
        $password = $_POST['psw'];

        $stmt = $conn->prepare("SELECT * FROM studentinfo WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                if ($user['remaining_sessions'] <= 0) {
                    $_SESSION['error'] = "You have no remaining sessions. Please contact the administrator.";
                    header("Location: login.html");
                    exit();
                }

                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['student_id'];
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['error'] = "Incorrect password!";
                header("Location: login.html");
                exit();
            }
        } else {
            $_SESSION['error'] = "Username not found!";
            header("Location: login.html");
            exit();
        }

        $stmt->close();
        $conn->close();
    } else {
        $_SESSION['error'] = "Please enter both username and password!";
        header("Location: login.html");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: login.html");
    exit();
}
?>
