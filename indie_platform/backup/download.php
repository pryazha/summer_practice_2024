<?php
require 'config.php';
session_start();

$assets = $conn->query("SELECT id, title, description, file_path FROM assets");

while ($row = $assets->fetch_assoc()) {
    echo "<div>";
    echo "<h3>" . $row['title'] . "</h3>";
    echo "<p>" . $row['description'] . "</p>";
    echo "<a href='download_asset.php?id=" . $row['id'] . "'>Download</a>";
    echo "</div>";
}
?>
