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
    
    $stmt = $conn->prepare("SELECT title, description FROM assets WHERE id = ?");
    $stmt->bind_param("i", $asset_id);
    $stmt->execute();
    $stmt->bind_result($title, $description);
    $stmt->fetch();
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['asset_id'])) {
    $asset_id = $_POST['asset_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    $stmt = $conn->prepare("UPDATE assets SET title = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $description, $asset_id);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: user.php");
        exit();
    } else {
        $error_message = "Failed to update asset.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Asset</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php generateNavBar($conn); ?>

    <div class="container">
        <div class="header">
            <h2>Edit Asset</h2>
        </div>

        <div class="form-container">
            <form method="post">
                <input type="hidden" name="asset_id" value="<?php echo $asset_id; ?>">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
                
                <button type="submit">Update Asset</button>
            </form>
            <?php
            if (!empty($error_message)) {
                echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
