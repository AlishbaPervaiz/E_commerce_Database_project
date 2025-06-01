<?php
session_start();

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
  <title>ShopZone - Your Favorite Online Store</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .navbar-custom {
      background-color: #fff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 24px;
    }
    .icon {
      width: 24px;
      height: 24px;
      fill: #333;
    }
    .hero {
      background: url('https://images.pexels.com/photos/14484177/pexels-photo-14484177.jpeg?auto=compress&cs=tinysrgb&w=600') no-repeat center center/cover;
      height: 100vh;
       color: white;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .hero h1 {
      font-size: 48px;
      font-weight: 700;
    }
    .hero p {
      font-size: 20px;
    }
    .section-title {
      text-align: center;
      margin-top: 60px;
      margin-bottom: 30px;
      font-weight: 600;
    }
    footer {
      background-color: #333;
      color: white;
      padding: 30px 0;
      text-align: center;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light navbar-custom px-4">
  <a class="navbar-brand" href="#">ShopZone</a>
  <div class="ms-auto d-flex align-items-center">
    <ul class="navbar-nav d-flex flex-row align-items-center gap-4">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="#">About</a></li>
      <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>

      <!-- Cart Icon -->
    <!-- Cart Icon -->
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

      <!-- Account Dropdown -->
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

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <h1>Welcome to ShopZone</h1>
    <p>Your one-stop destination for amazing deals</p>
  </div>
</section>

<!-- Collections -->
<section>
  <h2 class="section-title">Our Collections</h2>
  <div class="container text-center">
    <img src="https://images.pexels.com/photos/2983398/pexels-photo-2983398.jpeg?auto=compress&cs=tinysrgb&w=600" class="m-2" />
    <img src="https://images.pexels.com/photos/8938664/pexels-photo-8938664.jpeg?auto=compress&cs=tinysrgb&w=600" class="m-2" />
    <img src="https://images.pexels.com/photos/9219063/pexels-photo-9219063.jpeg?auto=compress&cs=tinysrgb&w=600" class="m-2" />
  </div>
</section>

<!-- Products -->
<section>
  <h2 class="section-title">Featured Products</h2>
  <div class="container text-center">
    <img src="https://images.pexels.com/photos/17916585/pexels-photo-17916585/free-photo-of-man-selling-souvenirs-in-the-street.jpeg?auto=compress&cs=tinysrgb&w=600" class="m-2" />
    <img src="https://images.pexels.com/photos/2536965/pexels-photo-2536965.jpeg?auto=compress&cs=tinysrgb&w=600" class="m-2" />
    <img src="https://images.pexels.com/photos/2547541/pexels-photo-2547541.jpeg?auto=compress&cs=tinysrgb&w=600" class="m-2" />
  </div>
</section>

<!-- Team -->
<section>
  <h2 class="section-title">Meet Our Team</h2>
  <div class="container text-center">
    <img src="https://images.pexels.com/photos/3184418/pexels-photo-3184418.jpeg?auto=compress&cs=tinysrgb&w=600" class="rounded-circle m-2" />
    <img src="https://images.pexels.com/photos/3184423/pexels-photo-3184423.jpeg?auto=compress&cs=tinysrgb&w=600" class="rounded-circle m-2" />
  </div>
</section>

<!-- Testimonials -->
<section>
  <h2 class="section-title">Testimonials</h2>
  <div class="container text-center">
    <blockquote>"Fantastic shopping experience!"</blockquote>
    <blockquote>"Great products and fast delivery."</blockquote>
  </div>
</section>

<!-- Footer -->
<footer>
  <p>&copy; 2025 ShopZone. All rights reserved.</p>
</footer>

<!-- Bootstrap JS Bundle (Dropdown support) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
