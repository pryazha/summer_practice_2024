<?php
require 'config.php';
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Indie Game and Asset Platform</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to the Indie Game and Asset Platform</h1>
            <?php if (isLoggedIn()): ?>
                <p>Welcome, user! You are logged in.</p>
                <p><a href="upload.php">Upload a new asset</a> | <a href="logout.php">Logout</a></p>
            <?php else: ?>
                <p><a href="register.php">Register</a> | <a href="login.php">Login</a></p>
            <?php endif; ?>
        </div>

        <h2 class="assets">Available Assets</h2>
        <div class="assets">
            <?php
            $assets = $conn->query("SELECT id, title, description FROM assets");

            if ($assets->num_rows > 0) {
                while ($row = $assets->fetch_assoc()) {
                    echo "<div class='tile'>";
                    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                    echo "<a href='download_asset.php?id=" . $row['id'] . "'>Download</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No assets available.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
