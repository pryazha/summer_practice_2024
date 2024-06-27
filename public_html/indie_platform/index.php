<?php
require('config.php');
session_start();
require_once('include/functions.php');

$assets_result = $conn->query("SELECT id, title, description, file_path, image_path FROM assets");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Indie Game and Asset Platform</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php generateNavBar($conn) ?>
    <div class="container">
        <div class="assets" id="assets">
            <?php if ($assets_result->num_rows > 0): ?>
                <?php while ($row = $assets_result->fetch_assoc()): ?>
                <div class="tile">
                    <?php if ($row['image_path']): ?>
                    <img class="asset-image" src="<?php echo $row['image_path']; ?>" alt="Asset Image">
                    <?php endif; ?>
                    <h3><a href="asset.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h3>
                    <p class="asset-description"><?php echo htmlspecialchars($row['description']); ?></p>
                    <a href="<?php echo $row['file_path']; ?>" download>Download</a>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No assets available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
