<?php
require 'config.php';
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Обработка создания новой темы
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['message'])) {
    $title = $_POST['title'];
    $message = $_POST['message'];

    // Добавляем новую тему в базу данных
    $stmt = $conn->prepare("INSERT INTO forum_topics (title) VALUES (?)");
    $stmt->bind_param("s", $title);

    if ($stmt->execute()) {
        $new_topic_id = $stmt->insert_id;
        $stmt->close();

        // Добавляем первое сообщение в эту тему
        $stmt = $conn->prepare("INSERT INTO forum_messages (topic_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $new_topic_id, $_SESSION['user_id'], $message);

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
$topics_result = $conn->query("SELECT id, title FROM forum_topics ORDER BY id DESC");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Forum</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Forum</h1>
            <?php if (isLoggedIn()): ?>
                <p>Welcome, user! You are logged in.</p>
                <p><a href="index.php">Back to Home</a> | <a href="logout.php">Logout</a></p>
            <?php else: ?>
                <p><a href="register.php">Register</a> | <a href="login.php">Login</a></p>
            <?php endif; ?>
        </div>

        <div class="forum">
            <!-- Форма для создания новой темы -->
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

            <!-- Список тем форума -->
            <h2>Forum Topics</h2>
            <ul class="topics">
                <?php if ($topics_result->num_rows > 0): ?>
                    <?php while ($row = $topics_result->fetch_assoc()): ?>
                        <li><a href="topic.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No topics available.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>

