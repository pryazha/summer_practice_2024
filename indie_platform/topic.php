<?php
require('config.php');
session_start();
require_once('include/functions.php');

$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$topic_stmt = $conn->prepare("SELECT ft.title, u.username, ft.created_at FROM forum_topics ft JOIN users u ON ft.user_id = u.id WHERE ft.id = ?");
$topic_stmt->bind_param("i", $topic_id);
$topic_stmt->execute();
$topic_stmt->bind_result($topic_title, $topic_username, $topic_created_at);
$topic_stmt->fetch();
$topic_stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $message = $_POST['message'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO forum_messages (topic_id, user_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $topic_id, $user_id, $message);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: topic.php?id=$topic_id");
        exit();
    } else {
        echo "Error adding message: " . $stmt->error;
    }
}

$messages_result = $conn->query("SELECT fm.message, u.username, fm.created_at FROM forum_messages fm JOIN users u ON fm.user_id = u.id WHERE fm.topic_id = $topic_id ORDER BY fm.created_at ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($topic_title); ?> - Forum</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php generateNavBar($conn) ?>
    <div class="container">
        <div class="topic">
            <h2><?php echo htmlspecialchars($topic_title); ?></h2>
            <p>Posted by <?php echo htmlspecialchars($topic_username); ?> on <?php echo $topic_created_at; ?></p>

            <div class="messages">
                <?php if ($messages_result->num_rows > 0): ?>
                    <?php while ($row = $messages_result->fetch_assoc()): ?>
                        <div class="message">
                            <p><?php echo htmlspecialchars($row['message']); ?></p>
                            <p>Posted by <?php echo htmlspecialchars($row['username']); ?> on <?php echo $row['created_at']; ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No messages available.</p>
                <?php endif; ?>
            </div>

            <?php if (isLoggedIn()): ?>
                <div class="form-container">
                    <h3>Add a new message</h3>
                    <form method="post">
                        <textarea name="message" rows="4" cols="50" required></textarea><br>
                        <button type="submit">Post Message</button>
                    </form>
                </div>
            <?php else: ?>
                <p><a href="login.php">Login</a> to post messages.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

