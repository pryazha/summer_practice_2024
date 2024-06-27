<?php
require('config.php');
session_start();
require_once('include/functions.php');

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $asset_id = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT title, file_path, image_path FROM assets WHERE id = ?");
    $stmt->bind_param("i", $asset_id);
    $stmt->execute();
    $stmt->bind_result($title, $file_path, $image_path);
    $stmt->fetch();
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['asset_id'])) {
    $asset_id = $_POST['asset_id'];
    $file_path = $_POST['file_path'];
    $image_path = $_POST['image_path'];
    
    $conn->begin_transaction();
    
    $stmt_delete_comments = $conn->prepare("DELETE FROM asset_comments WHERE asset_id = ?");
    $stmt_delete_comments->bind_param("i", $asset_id);
    $stmt_delete_comments->execute();
    $stmt_delete_comments->close();
    
    $stmt_delete_downloads = $conn->prepare("DELETE FROM downloads WHERE asset_id = ?");
    $stmt_delete_downloads->bind_param("i", $asset_id);
    $stmt_delete_downloads->execute();
    $stmt_delete_downloads->close();
    
    $stmt_delete_asset = $conn->prepare("DELETE FROM assets WHERE id = ?");
    $stmt_delete_asset->bind_param("i", $asset_id);
    if ($stmt_delete_asset->execute()) {
        $stmt_delete_asset->close();
        
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }
        
        $conn->commit();
        
        header("Location: user.php");
        exit();
    } else {
        $conn->rollback();
        $error_message = "Failed to delete asset.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Delete Asset</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php generateNavBar($conn); ?>

    <div class="container">
        <div class="header">
            <h2>Delete Asset</h2>
        </div>

        <div class="form-container">
            <?php if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])): ?>
                <p>Are you sure you want to delete the asset "<strong><?php echo htmlspecialchars($title); ?></strong>"?</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="asset_id" value="<?php echo $asset_id; ?>">
                    <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($file_path); ?>">
                    <input type="hidden" name="image_path" value="<?php echo htmlspecialchars($image_path); ?>">
                    <button type="submit">Delete</button>
                    <a href="user.php">Cancel</a>
                </form>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
