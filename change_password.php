<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE User SET password = ? WHERE user_id = ?");
    $stmt->bind_param("ss", $new_password, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Password changed successfully.'); window.location.href='account_info.php';</script>";
    } else {
        echo "<script>alert('Error updating password.'); window.location.href='account_info.php';</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h2>Change Password</h2>
  <form method="post" action="">
    <div class="mb-3">
      <label for="new_password" class="form-label">New Password</label>
      <input type="password" class="form-control" name="new_password" required>
    </div>
    <button type="submit" class="btn btn-primary">Change Password</button>
  </form>
</div>
</body>
</html>
