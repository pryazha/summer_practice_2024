<?php
require 'config.php';
session_start();
require('include/functions.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['description']) && isset($_FILES['file']) && isset($_FILES['image'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file = $_FILES['file'];
    $image = $_FILES['image'];
    $user_id = $_SESSION['user_id'];

    $file_path = 'uploads/' . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        $image_path = 'uploads/' . basename($image['name']);
        if (move_uploaded_file($image['tmp_name'], $image_path)) {
            $stmt = $conn->prepare("INSERT INTO assets (title, description, file_path, image_path, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $title, $description, $file_path, $image_path, $user_id);

            if ($stmt->execute()) {
                $status = "Asset uploaded successfully.";
            } else {
                $status = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $status = "Failed to upload image.";
        }
    } else {
        $status = "Failed to upload file.";
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
    <?php generateNavBar($conn) ?>
    <div class="container">
        <div class="header">
            <h1>Upload Asset</h1>
        </div>
        <div class="form-container">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <label for="title">Title:</label><br>
                <input type="text" name="title" id="title" required><br><br>
                
                <label for="description">Description:</label><br>
                <textarea name="description" id="description" required></textarea><br><br>
                
                <label for="file">Asset File:</label><br>
                <input type="file" name="file" id="file" required><br><br>

                <label for="image">Asset Image:</label><br>
                <input type="file" name="image" id="image" required><br><br>
                
                <button type="submit">Upload</button>
            </form><br>
        <h3><?php if (isset($status)) { echo $status; } ?></h3>
        </div>
    </div>
</body>
</html>
