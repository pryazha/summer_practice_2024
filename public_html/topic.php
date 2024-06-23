<?php
require 'config.php';
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid topic ID.");
}

$topic_id = $_GET['id'];

// Получаем информацию о теме
$stmt = $conn->prepare("SELECT title FROM forum_topics WHERE id = ?");
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("Topic not found.");
}

$stmt->bind_result($topic_title);
$stmt->fetch();
$stmt->close();

// Получаем сообщения в теме
$messages = $conn->prepare("SELECT id, user_id, message FROM forum_messages WHERE topic_id = ?");
$messages->bind_param("i", $topic_id);
$messages->execute();
$messages_result = $messages->get_result();
$messages->close();

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($topic_title); ?> - Forum Topic</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo htmlspecialchars($topic_title); ?></h1>
            <?php if (isLoggedIn()): ?>
                <p>Welcome, user! You are logged in.</p>
                <p><a href="index.php">Back to Home</a> | <a href="logout.php">Logout</a></p>
            <?php else: ?>
                <p><a href="register.php">Register</a> | <a href="login.php">Login</a></p>
            <?php endif; ?>
        </div>

        <div class="forum">
            <?php if ($messages_result->num_rows > 0): ?>
                <ul class="messages">
                    <?php while ($row = $messages_result->fetch_assoc()): ?>
                        <li>
                            <strong>User <?php echo $row['user_id']; ?>:</strong>
                            <p><?php echo htmlspecialchars($row['message']); ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No messages in this topic yet.</p>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <div class="form-container">
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
