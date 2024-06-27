<?php
require('config.php');
session_start();
require_once('include/functions.php');

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_info = getUserInfo($conn, $user_id);
$username = $user_info['username'];

$assets_stmt = $conn->prepare("SELECT id, title, description, file_path, image_path FROM assets WHERE user_id = ?");
$assets_stmt->bind_param("i", $user_id);
$assets_stmt->execute();
$assets_result = $assets_stmt->get_result();
$assets_stmt->close();

?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php generateNavBar($conn); ?>
        <div class="container">
            <div class="profile">
                <h2>User Profile: <?php echo htmlspecialchars($username); ?></h2>

                <div class="user-details">
                    <p>Username: <?php echo htmlspecialchars($username); ?></p>
                    <p>Email: <?php echo htmlspecialchars($user_info['email']); ?></p>
                </div>

                <h3>Uploaded Assets</h3>
                <div class="assets">
                    <?php if ($assets_result->num_rows > 0): ?>
                    <?php while ($row = $assets_result->fetch_assoc()): ?>
                    <div class="tile">
                        <div class="asset-image">
                            <img src="<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        </div>
                        <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                        <p class="asset-description"><?php echo htmlspecialchars($row['description']); ?></p>
                        <a href="<?php echo $row['file_path']; ?>" download>Download</a>
                        <a href="edit_asset.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="delete_asset.php?id=<?php echo $row['id']; ?>">Delete</a>
                    </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <p>No assets uploaded.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </body>
</html>
