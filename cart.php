<?php
session_start();
include 'connect.php';

// Initialize cart
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Fetch all product IDs in the cart
$productDetails = [];
$totalPrice = 0;

if (!empty($cart)) {
    $ids = implode(",", array_map('intval', array_keys($cart)));
    $sql = "SELECT * FROM Product WHERE product_id IN ($ids)";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pid = $row['product_id'];
            $qty = $cart[$pid];
            $row['quantity'] = $qty;
            $row['subtotal'] = $qty * $row['price'];
            $productDetails[] = $row;
            $totalPrice += $row['subtotal'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Your Cart - ShopZone</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .cart-table img { width: 80px; height: auto; border-radius: 5px; }
    .cart-table th, .cart-table td { vertical-align: middle !important; }
    footer { background-color: #333; color: white; padding: 30px 0; text-align: center; margin-top: 50px; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
  <a class="navbar-brand" href="index.php">ShopZone</a>
  <div class="ms-auto">
    <a href="product.php" class="btn btn-outline-primary me-2">Continue Shopping</a>
  </div>
</nav>

<div class="container mt-5">
  <h2 class="mb-4 text-center">Your Shopping Cart</h2>

  <?php if (!empty($productDetails)): ?>
    <div class="table-responsive">
      <table class="table cart-table table-bordered align-middle">
        <thead class="table-secondary">
          <tr>
            <th>Product</th>
            <th>Thumbnail</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($productDetails as $product): ?>
            <tr>
              <td><?php echo htmlspecialchars($product['name']); ?></td>
              <td><img src="<?php echo $product['product_image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"></td>
              <td>$<?php echo number_format($product['price'], 2); ?></td>
              <td><?php echo $product['quantity']; ?></td>
              <td><strong>$<?php echo number_format($product['subtotal'], 2); ?></strong></td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="4" class="text-end"><strong>Total:</strong></td>
            <td><strong>$<?php echo number_format($totalPrice, 2); ?></strong></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="text-end mt-3">
      <a href="checkout.php" class="btn btn-success btn-lg">Proceed to Checkout</a>
    </div>
  <?php else: ?>
    <p class="text-center">Your cart is empty. <a href="product.php">Start shopping</a>.</p>
  <?php endif; ?>
</div>

<footer>
  <p>&copy; 2025 ShopZone. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
