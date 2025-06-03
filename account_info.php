<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include 'connect.php';

$username = $_SESSION['username'];

// Fetch user details using username now
$sql = "SELECT name, email, contact, address FROM User WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "<p>User not found.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Info</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h2>Your Account Information</h2>
  <table class="table table-bordered mt-3">
    <tr><th>Username</th><td><?php echo htmlspecialchars($username); ?></td></tr>
    <tr><th>Name</th><td><?php echo htmlspecialchars($user['name']); ?></td></tr>
    <tr><th>Email</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
    <tr><th>Contact</th><td><?php echo htmlspecialchars($user['contact']); ?></td></tr>
    <tr><th>Address</th><td><?php echo htmlspecialchars($user['address']); ?></td></tr>
  </table>

  <a href="change_password.php" class="btn btn-warning">Change Password</a>
  <a href="edit_info_form.php" class="btn btn-primary">Edit Information</a>
  <a href="logout.php" class="btn btn-danger">Logout</a>
  <a href="delete_account.php" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your account? This action is irreversible.');">Delete Account</a>
</div>
</body>
</html>