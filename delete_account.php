<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include 'connect.php';
$username = $_SESSION['username'];

// Call the stored procedure to delete the account and all related data
$stmt = $conn->prepare("CALL DeleteUserAccount(?)");
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    session_destroy();
    echo "<script>alert('Your account has been deleted successfully.'); window.location.href = 'index.php';</script>";
} else {
    echo "<script>alert('Failed to delete your account.'); window.location.href = 'account_info.php';</script>";
}

$stmt->close();
$conn->close();
?>
