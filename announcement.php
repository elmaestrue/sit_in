<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $conn->query("INSERT INTO announcements (title, content) VALUES ('$title', '$content')");
    echo "Announcement created!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Announcement</title>
</head>
<body>
    <h2>Create Announcement</h2>
    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" required>
        <br>
        <label>Content:</label>
        <textarea name="content" required></textarea>
        <br>
        <button type="submit">Post</button>
    </form>
</body>
</html>
