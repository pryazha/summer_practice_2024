<?php
require('config.php');
session_start();
require('include/functions.php');

$asset_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$asset_stmt = $conn->prepare("SELECT title, description, file_path, user_id FROM assets WHERE id = ?");
$asset_stmt->bind_param("i", $asset_id);
$asset_stmt->execute();
$asset_stmt->bind_result($title, $description, $file_path, $user_id);
$asset_stmt->fetch();
$asset_stmt->close();

$author_username = getUsername($conn, $user_id);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    $rating = $_POST['rating'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO asset_comments (asset_id, user_id, comment, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $asset_id, $user_id, $comment, $rating);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: asset.php?id=$asset_id");
        exit();
    } else {
        echo "Error adding comment: " . $stmt->error;
    }
}

$comments_result = $conn->query("SELECT ac.comment, ac.rating, u.username, ac.created_at FROM asset_comments ac JOIN users u ON ac.user_id = u.id WHERE ac.asset_id = $asset_id ORDER BY ac.created_at ASC");

$user_comment_stmt = $conn->prepare("SELECT id FROM asset_comments WHERE asset_id = ? AND user_id = ?");
$user_comment_stmt->bind_param("ii", $asset_id, $_SESSION['user_id']);
$user_comment_stmt->execute();
$user_comment_stmt->store_result();
$user_has_commented = $user_comment_stmt->num_rows > 0;
$user_comment_stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php generateNavBar($conn) ?>
    <div class="container">
        <div class="asset-detail">
            <h2><?php echo htmlspecialchars($title); ?></h2>
            <p><?php echo htmlspecialchars($description); ?></p>
            <p>Uploaded by <?php echo htmlspecialchars($author_username); ?></p>
            <a href="<?php echo $file_path; ?>" download>Download</a>

            <!-- Список комментариев -->
            <div class="comments">
                <h3>Comments</h3>
                <?php if ($comments_result->num_rows > 0): ?>
                    <?php while ($row = $comments_result->fetch_assoc()): ?>
                        <div class="comment">
                            <p><?php echo htmlspecialchars($row['comment']); ?></p>
                            <p>Rating: <?php echo $row['rating']; ?>/5</p>
                            <p>Posted by <?php echo htmlspecialchars($row['username']); ?> on <?php echo $row['created_at']; ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No comments available.</p>
                <?php endif; ?>
            </div>

            <?php if (isLoggedIn() && !$user_has_commented): ?>
                <div class="form-container">
                    <h3>Add a new comment</h3>
                    <form method="post">
                        <textarea name="comment" rows="4" cols="50" required></textarea><br>
                        <label for="rating">Rating:</label>
                        <select name="rating" id="rating" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select><br><br>
                        <button type="submit">Post Comment</button>
                    </form>
                </div>
            <?php elseif (isLoggedIn()): ?>
                <p>You have already commented on this asset.</p>
            <?php else: ?>
                <p><a href="login.php">Login</a> to post comments.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
