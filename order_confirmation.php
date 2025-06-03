<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Order Confirmation</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .confirmation-box {
      max-width: 600px;
      margin: 2rem auto;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      background: white;
    }
    .icon-large {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: #28a745;
    }
    .feedback-note {
      background-color: #f8f9fa;
      border-left: 4px solid #17a2b8;
      padding: 1rem;
      margin: 1.5rem 0;
      text-align: left;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container">
    <div class="confirmation-box text-center">
      <div class="icon-large">🎉</div>
      <h2 class="mb-3">Thank you for your order!</h2>
      <p class="lead">Your order has been successfully placed and is being processed.</p>
      
      <div class="feedback-note">
        <h5><i class="fas fa-comment-alt"></i> Help us improve!</h5>
        <p>Once your order is delivered, please visit your <strong>Order History</strong> to:</p>
        <ol class="text-start">
          <li>Click on the <strong>"Give Feedback"</strong> button</li>
          <li>Share your experience with us</li>
          <li>Rate the products you received</li>
        </ol>
        <p class="mb-0">Your feedback helps us serve you better!</p>
      </div>
      
      <div class="d-grid gap-2 d-md-block mt-3">
        <a href="product.php" class="btn btn-primary me-md-2">
          <i class="fas fa-shopping-bag"></i> Continue Shopping
        </a>
        <a href="order_history.php" class="btn btn-outline-secondary">
          <i class="fas fa-history"></i> View Order History
        </a>
      </div>
    </div>
  </div>

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>