<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'connect.php';

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT name, email, contact, address FROM User WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
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
    <tr><th>Name</th><td><?php echo htmlspecialchars($user['name']); ?></td></tr>
    <tr><th>Email</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
    <tr><th>Contact</th><td><?php echo htmlspecialchars($user['contact']); ?></td></tr>
    <tr><th>Address</th><td><?php echo htmlspecialchars($user['address']); ?></td></tr>
  </table>

  <a href="change_password.php" class="btn btn-warning">Change Password</a>
  <a href="edit_info_form.php" class="btn btn-primary">Edit Information</a>
  <a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</body>
</html>
