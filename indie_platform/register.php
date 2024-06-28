<?php
require('config.php');
session_start();
require_once('include/functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        header("Location: index.php");
        exit();
    } else {
        $status = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php generateNavBar($conn) ?>
    <div class="container">
        <div class="header">
            <h1>Register</h1>
        </div>
        <div class="form-container">
            <form action="register.php" method="post">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required><br>
                
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required><br>
                
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required><br>
                
                <button type="submit">Register</button>
            </form><br>
            <h3><?php if (isset($status)) { echo $status; } ?></h3>
        </div>
    </div>
</body>
</html>

