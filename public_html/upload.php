<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to upload files.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file = $_FILES['file'];

    $upload_directory = 'uploads/';
    $file_path = $upload_directory . basename($file['name']);

    if (file_exists($file_path)) {
        echo "Sorry, file already exists.";
        exit;
    }

    $allowed_types = ['image/jpeg', 'image/png', 'application/zip', 'application/x-rar-compressed'];
    if (!in_array($file['type'], $allowed_types)) {
        echo "Sorry, only JPG, PNG, ZIP and RAR files are allowed.";
        exit;
    }

    $max_file_size = 10 * 1024 * 1024;
    if ($file['size'] > $max_file_size) {
        echo "Sorry, your file is too large.";
        exit;
    }

    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        $stmt = $conn->prepare("INSERT INTO assets (user_id, title, description, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $_SESSION['user_id'], $title, $description, $file_path);

        if ($stmt->execute()) {
            echo "File uploaded successfully!";
            echo '<p><a href="index.php">Back to Home</a></p>';
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Failed to upload file.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Asset</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Upload Asset</h1>
        </div>
        <div class="form-container">
            <form method="post" enctype="multipart/form-data">
                Title: <input type="text" name="title" required><br>
                Description: <textarea name="description" required></textarea><br>
                File: <input type="file" name="file" required><br>
                <button type="submit">Upload</button>
            </form>
            <p><a href="index.php">Back to Home</a></p>
        </div>
    </div>
</body>
</html>
