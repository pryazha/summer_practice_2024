<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to download files.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT file_path FROM assets WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($file_path);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        $file_name = basename($file_path);
        header("Content-Disposition: attachment; filename=$file_name");
        readfile($file_path);

        $stmt = $conn->prepare("INSERT INTO downloads (user_id, asset_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $_SESSION['user_id'], $id);
        $stmt->execute();
    } else {
        echo "File not found.";
    }
}
?>
