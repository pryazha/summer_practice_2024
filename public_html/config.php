<?php
$servername = "localhost";
$username = "pryazha";
$password = "89f519gh";
$dbname = "indie_platform";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
