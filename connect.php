<?php
$host = "localhost";     // your DB host
$user = "root";          // DB username
$pass = "";              // DB password
$db   = "ecommerce_db"; // change this to your actual DB name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
