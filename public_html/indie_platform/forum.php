<?php
require('config.php');
session_start();
require('include/functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['message'])) {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO forum_topics (title, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $title, $user_id);

    if ($stmt->execute()) {
        $new_topic_id = $stmt->insert_id;
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO forum_messages (topic_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $new_topic_id, $user_id, $message);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: forum.php");
            exit();
        } else {
            echo "Error adding message: " . $stmt->error;
        }
    } else {
        echo "Error adding topic: " . $stmt->error;
    }
}

// Получаем список тем
$topics_result = $conn->query("SELECT ft.id, ft.title, u.username, ft.created_at FROM forum_topics ft JOIN users u ON ft.user_id = u.id ORDER BY ft.created_at DESC");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Forum</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php generateNavBar($conn) ?>
    <div class="container">
        <div class="forum">
            <?php if (isLoggedIn()): ?>
                <div class="form-container">
                    <h2>Create New Topic</h2>
                    <form method="post">
                        Title: <input type="text" name="title" required><br>
                        Message: <textarea name="message" rows="4" cols="50" required></textarea><br>
                        <button type="submit">Create Topic</button>
                    </form>
                </div>
            <?php else: ?>
                <p><a href="login.php">Login</a> to create new topics.</p>
            <?php endif; ?>

            <h2>Forum Topics</h2>
            <div class="topics">
                <?php if ($topics_result->num_rows > 0): ?>
                    <?php while ($row = $topics_result->fetch_assoc()): ?>
                        <div class="topic">
                            <h3><a href="topic.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h3>
                            <p>Posted by <?php echo htmlspecialchars($row['username']); ?> on <?php echo $row['created_at']; ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No topics available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

