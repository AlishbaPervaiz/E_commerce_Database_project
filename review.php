<?php
session_start();
include 'connect.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$userId = $_SESSION['user_id'];
$notification = "";

// Check for completed orders without review
$sqlPendingReview = "
    SELECT o.order_id 
    FROM `order` o 
    LEFT JOIN review r ON o.order_id = r.order_id 
    WHERE o.customer_id = $userId AND o.status = 'Confirmed' AND r.review_id IS NULL
    LIMIT 1
";
$pendingReviewResult = $conn->query($sqlPendingReview);
$showNotification = ($pendingReviewResult && $pendingReviewResult->num_rows > 0);

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = $_POST['order_id'];
    $rating = $_POST['rating'];
    $comment = $conn->real_escape_string($_POST['comment']);

    $insertReview = "
        INSERT INTO review (user_id, order_id, rating, comment) 
        VALUES ($userId, $orderId, $rating, '$comment')
    ";

    if ($conn->query($insertReview)) {
        $notification = "Thank you for your feedback!";
    } else {
        $notification = "Failed to submit review.";
    }
}

// Fetch all confirmed & reviewed orders
$sqlOrders = "
    SELECT o.order_id, o.order_date 
    FROM `order` o
    LEFT JOIN review r ON o.order_id = r.order_id
    WHERE o.customer_id = $userId AND o.status = 'Confirmed' AND r.review_id IS NULL
";
$ordersResult = $conn->query($sqlOrders);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Review - ShopZone</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
  <a class="navbar-brand" href="index.php">ShopZone</a>
  <div class="ms-auto">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <?php if ($showNotification): ?>
        <li class="nav-item">
          <a class="nav-link text-danger" href="review.php">
            You have a pending review!
          </a>
        </li>
      <?php endif; ?>
      <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <h3>Submit Your Review</h3>
  <?php if ($notification): ?>
    <div class="alert alert-info"><?php echo $notification; ?></div>
  <?php endif; ?>

  <?php if ($ordersResult->num_rows > 0): ?>
    <form method="post" class="mt-4">
      <div class="mb-3">
        <label for="order_id" class="form-label">Select Order</label>
        <select name="order_id" class="form-select" required>
          <?php while ($row = $ordersResult->fetch_assoc()): ?>
            <option value="<?php echo $row['order_id']; ?>">
              Order #<?php echo $row['order_id']; ?> - <?php echo $row['order_date']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="rating" class="form-label">Rating</label>
        <select name="rating" class="form-select" required>
          <option value="5">★★★★★ Excellent</option>
          <option value="4">★★★★☆ Good</option>
          <option value="3">★★★☆☆ Average</option>
          <option value="2">★★☆☆☆ Poor</option>
          <option value="1">★☆☆☆☆ Terrible</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="comment" class="form-label">Comment</label>
        <textarea name="comment" class="form-control" rows="4" required></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
  <?php else: ?>
    <p>No completed orders found or all reviews submitted.</p>
  <?php endif; ?>
</div>

</body>
</html>
