<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $message = $_POST['message'];

    // Check for foul words
    $foulWords = $conn->query("SELECT word FROM foul_words");
    while ($word = $foulWords->fetch_assoc()) {
        if (stripos($message, $word['word']) !== false) {
            echo "<script>alert('Your message contains inappropriate language!');</script>";
            exit();
        }
    }

    // Insert feedback if clean
    $conn->query("INSERT INTO feedback (student_id, message) VALUES ('$student_id', '$message')");
    echo "<script>alert('Feedback submitted!');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Feedback</title>
</head>
<body>
    <h2>Submit Feedback</h2>
    <form method="POST">
        <label>Student ID:</label>
        <input type="number" name="student_id" required>
        <br>
        <label>Feedback:</label>
        <textarea name="message" required></textarea>
        <br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
