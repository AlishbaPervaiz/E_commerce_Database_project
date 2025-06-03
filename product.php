
<?php
session_start();
include 'connect.php';

// Initialize cart if not already
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = max(1, intval($_POST['quantity']));

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }

    $_SESSION['added_to_cart'] = true;
    header("Location: product.php");
    exit();
}

// Fetch available products from database
global $conn;
$sql = "SELECT * FROM Product WHERE is_available = 1";
$result = $conn->query($sql);
$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get cart count
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Products - ShopZone</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .navbar-custom { background-color: #fff; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
    .navbar-brand { font-weight: 700; font-size: 24px; }
    .icon { width: 24px; height: 24px; fill: #333; }
    .section-title { text-align: center; margin-top: 60px; margin-bottom: 30px; font-weight: 600; }
    .product-card { border: 1px solid #ddd; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    footer { background-color: #333; color: white; padding: 30px 0; text-align: center; margin-top: 50px; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light navbar-custom px-4">
  <a class="navbar-brand" href="index.php">ShopZone</a>
  <div class="ms-auto d-flex align-items-center">
    <ul class="navbar-nav d-flex flex-row align-items-center gap-4">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="#">About</a></li>
      <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
      <li class="nav-item">
        <a href="cart.php" class="nav-link" aria-label="Cart">
          <svg class="icon" viewBox="0 0 24 24">
            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm0
              2m10-2c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3h11.17c.55 0
              1.04-.35 1.21-.87l2.58-7.49a.996.996 0 0 0-.96-1.34H6.21L5.27 4H2v2h2l3.6 
              7.59-1.35 2.44c-.18.32-.27.69-.27 1.07 0 1.1.9 2 2 2z"/>
          </svg>
          <span id="cart-count"><?php echo $cartCount; ?></span>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <svg class="icon" viewBox="0 0 24 24">
            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2
              4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6
              4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
          </svg>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
          <?php if (isset($_SESSION['user_id'])): ?>
            <li><a class="dropdown-item" href="account_info.php">Account Info</a></li>
            <li><a class="dropdown-item" href="#">Order History</a></li>
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a class="dropdown-item" href="login.html">Login</a></li>
            <li><a class="dropdown-item" href="signup.html">Sign Up</a></li>
          <?php endif; ?>
        </ul>
      </li>
    </ul>
  </div>
</nav>

<!-- Product Listing -->
<section>
  <h2 class="section-title">All Products</h2>
  <div class="container">
    <div class="row">
      <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4">
          <div class="product-card text-center">
           <div class="product-image-wrapper mb-3">
  <img src="<?php echo $product['product_image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
</div>

            <h5><?php echo htmlspecialchars($product['name']); ?></h5>
            <p class="text-muted">Category: <?php echo htmlspecialchars($product['product_category']); ?></p>
            <p><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <form method="post" class="mt-2">
              <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
              <input type="number" name="quantity" value="1" min="1" class="form-control mb-2" style="width: 80px; margin: auto;">
              <button type="submit" class="btn btn-sm btn-outline-primary">Add to Cart</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Footer -->
<footer>
  <p>&copy; 2025 ShopZone. All rights reserved.</p>
</footer>

<?php if (isset($_SESSION['added_to_cart'])): ?>
  <script>
    alert("Item has been added to your cart!");
  </script>
  <?php unset($_SESSION['added_to_cart']); ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
  .product-image-wrapper {
  width: 100%;
  height: 200px; /* or any fixed height you prefer */
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f8f8f8;
  border-radius: 8px;
}

.product-image-wrapper img {
  max-height: 100%;
  max-width: 100%;
  object-fit: contain;
}

</style>
</body>
</html>
