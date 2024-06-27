<?php
session_start();
$title = "Learning";

include("include/header.php");
require_once("include/functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password']; // TODO: validate this!

    if ($email == false) {
        $status = 'Please enter a valid email address.';
    }
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id == false) {
    $id = 2;
}
?>

<form method="POST">
    <label for="email">Email:</label>
    <input type="text" name="email" id="email" /><br><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" /><br><br>

    <input type="submit" value="Login" /><br><br>
</form>

<?php
if (isset($status)) {
    echo $status;
}
?><br><br>

<?php
echo $id;
?>

<?php include("include/footer.php"); ?>
