<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']); // Can be email or username
    $password = $_POST['password'];

    // Prepare SQL to search by either email or username (case-insensitive)
    $sql = "SELECT * FROM user WHERE LOWER(email) = LOWER(?) OR LOWER(username) = LOWER(?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];

        header("Location: index.php");
        exit();
    } else {
        // Login failed
        echo "<script>
            alert('Invalid username/email or password. Please try again.');
            window.location.href='login.html';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
