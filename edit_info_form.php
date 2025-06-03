<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
include 'connect.php';

$username = $_SESSION['username'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    // Call stored procedure
    $stmt = $conn->prepare("CALL EditUserInfo(?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $name, $email, $contact, $address);

    if ($stmt->execute()) {
        echo "<script>alert('Information updated successfully.'); window.location.href='account_info.php';</script>";
    } else {
        echo "<script>alert('Failed to update information.'); window.location.href='edit_info_form.php';</script>";
    }
    $stmt->close();
    $conn->close();
    exit();
}

// Fetch current user info
$stmt = $conn->prepare("SELECT username, name, email, contact, address FROM User WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($username, $name, $email, $contact, $address);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Information</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Edit Your Information</h2>
  <form method="post" action="">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Contact</label>
      <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($contact) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Address</label>
      <textarea name="address" class="form-control" required><?= htmlspecialchars($address) ?></textarea>
    </div>
    <button type="submit" class="btn btn-success">Update Info</button>
    <a href="account_info.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>
