<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUsername($conn, $user_id) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();
    return $username;
}

function getUserInfo($conn, $user_id) {
    $stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $email, $created_at);
    $stmt->fetch();
    $stmt->close();
    return ['username' => $username,
        'email' => $email,
        'created_at' => $created_at];
}

function generateNavBar($conn) {
    echo '<nav class="nav">';
    echo '<ul>';
    echo '<li><a href="index.php">Assets</a></li>';
    echo '<li><a href="forum.php">Forum</a></li>';
    if (isLoggedIn()) {
        echo '<li><a href="upload.php">Upload</a></li>';
        echo '<li><a href="logout.php">Logout</a></li>';
    } else {
        echo '<li><a href="register.php">Register</a></li>';
        echo '<li><a href="login.php">Login</a></li>';
    }
    if (isLoggedIn()) {
        echo '<div class="user-info">';
        echo '<li><a href="user.php">' . htmlspecialchars(getUsername($conn, $_SESSION['user_id'])) . '</a></li>';
        echo '</div>';
    }
    echo '</ul>';
    echo '</nav>';
}
?>
