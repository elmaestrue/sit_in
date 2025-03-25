<?php
session_start(); // Start the session

// Destroy the session to log the user out
session_destroy();

// Check if the referrer (previous page) exists
if (isset($_SERVER['HTTP_REFERER'])) {
    // Redirect the user back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    // If no referrer, redirect to a default page (e.g., home page or login page)
    header("Location: index.php"); // Change this as needed
}

exit();
?>
