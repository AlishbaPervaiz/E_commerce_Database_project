<?php
session_start();
include 'connect.php';

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: product.php");
    exit();
}

// Fetch products in cart
$productIds = implode(",", array_keys($_SESSION['cart']));
$sql = "SELECT * FROM Product WHERE product_id IN ($productIds)";
$result = $conn->query($sql);
$cartItems = [];
$totalAmount = 0;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productId = $row['product_id'];
        $quantity = $_SESSION['cart'][$productIds];
        $subtotal = $quantity * $row['price'];
        $totalAmount += $subtotal;
        $row['quantity'] = $quantity;
        $row['subtotal'] = $subtotal;
        $cartItems[] = $row;
    }
}

// Set delivery fee
$deliveryFee = 50;
$totalAmount = 0;
$discount = 0;
$couponMessage = "";
$coupon_id = "NULL";
$now = date('Y-m-d'); // Needed for coupon validation


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['delivery_address'];
    $coupon_code = $_POST['coupon_code'];
    $couponId = "NULL";
 
    // OPTIONAL: Lookup coupon ID and discount (not implemented)
    // For now, we don’t apply any discount

$customerId = isset($_SESSION['user_id']) ? "'" . $conn->real_escape_string($_SESSION['user_id']) . "'" : "NULL";
    
// Recalculate total from cart for discount logic
$productIds = implode(",", array_keys($_SESSION['cart']));
$sql = "SELECT * FROM Product WHERE product_id IN ($productIds)";
$result = $conn->query($sql);
$cartItems = [];
$totalAmount = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productId = $row['product_id'];
        $quantity = $_SESSION['cart'][$productId];
        $subtotal = $quantity * $row['price'];
        $totalAmount += $subtotal;
        $row['quantity'] = $quantity;
        $row['subtotal'] = $subtotal;
        $cartItems[] = $row;
    }
}


// Coupon logic
 // Coupon logic
if (!empty($coupon_code)) {
    $couponQuery = "SELECT * FROM coupon WHERE coupon_code = ? AND starting_date <= ? AND expiry_date >= ?";
    $stmt = $conn->prepare($couponQuery);
    $stmt->bind_param("sss", $coupon_code, $now, $now);
    $stmt->execute();
    $couponResult = $stmt->get_result();

    if ($couponResult->num_rows > 0) {
        $coupon = $couponResult->fetch_assoc();
        if ($totalAmount >= $coupon['minimum_amount']) {
            $discount = $coupon['discount'];
            $coupon_id = $coupon['coupon_id'];
            $couponMessage = "Coupon applied! You saved $$discount.";
        } else {
            $couponMessage = "Minimum order must be $" . $coupon['minimum_amount'] . " to use this coupon.";
        }
    } else {
        $couponMessage = "Invalid or expired coupon.";
    }
}

    // Final amount after discount
    $grandTotal = $totalAmount + $deliveryFee - $discount;
    $orderDate = date('Y-m-d H:i:s');
    $customerId = isset($_SESSION['user_id']) ? "'" . $conn->real_escape_string($_SESSION['user_id']) . "'" : "NULL";


        // Insert order details
    $insertOrder = "INSERT INTO `order` (customer_id, full_name, email, contact, delivery_address, status, total_amount, discount, order_date, coupon_id)
VALUES ($customerId, '$fullName', '$email', '$contact', '$address', 'Pending', $grandTotal, $discount, '$orderDate', " . ($coupon_id !== "NULL" ? $coupon_id : "NULL") . ")";

if ($conn->query($insertOrder)) {
    $order_id = $conn->insert_id; 

    // Insert order details
    foreach ($cartItems as $item) {
        $pid = $item['product_id'];
        $price = $item['price'];
        $quantity = $item['quantity'];
        $insertDetail = "INSERT INTO `orderdetails` (order_id, product_id, price)
                         VALUES ($order_id, $pid, $price)";
        $conn->query($insertDetail);
    }

    // Clear cart and redirect
    unset($_SESSION['cart']);
    header("Location: order_confirmation.php");
    exit();
} else {
    echo "<script>alert('Error placing order. Please try again.');</script>";
}


        // Clear cart and redirect
        unset($_SESSION['cart']);
        header("Location: order_confirmation.php");
        exit();
    } else {
        echo "<script>alert('Error placing order. Please try again.');</script>";
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - ShopZone</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .checkout-container { max-width: 1200px; margin: 40px auto; }
    .order-summary { background: #f9f9f9; padding: 20px; border-radius: 10px; }
    .form-section { border-right: 1px solid #ccc; }
  </style>
</head>
<body>
<div class="container checkout-container">
  <div class="row">
    <!-- Checkout Form -->
    <div class="col-md-6 form-section">
      <h3>Checkout</h3>
      <form method="post">
        <div class="mb-3">
          <label for="full_name" class="form-label">Full Name</label>
          <input type="text" class="form-control" name="full_name" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
          <label for="contact" class="form-label">Contact</label>
          <input type="text" class="form-control" name="contact" required>
        </div>
        <div class="mb-3">
          <label for="delivery_address" class="form-label">Delivery Address</label>
          <textarea class="form-control" name="delivery_address" rows="3" required></textarea>
        </div>
        <div class="mb-3">
          <label for="code" class="form-label">Coupon Code</label>
          <input type="text" class="form-control" name="coupon_code">
        </div>
        <button type="submit" class="btn btn-success w-100">Confirm Order</button>
      </form>
    </div>

    <!-- Order Summary -->
     
    <div class="col-md-6">
      <div class="order-summary">
        <h4>Order Summary</h4>
     <ul class="list-group mb-3">
  <?php foreach ($cartItems as $item): ?>
    <li class="list-group-item d-flex justify-content-between">
      <div>
        <strong><?php echo $item['name']; ?></strong><br>
        x<?php echo $item['quantity']; ?>
      </div>
      <span>$<?php echo number_format($item['subtotal'], 2); ?></span>
    </li>
  <?php endforeach; ?>
  
  <li class="list-group-item d-flex justify-content-between">
    <span>Subtotal</span>
    <span>$<?php echo number_format($totalAmount, 2); ?></span>
  </li>

  <li class="list-group-item d-flex justify-content-between">
    <span>Delivery Fee</span>
    <span>$<?php echo number_format($deliveryFee, 2); ?></span>
  </li>

  <?php if ($discount > 0): ?>
    <li class="list-group-item d-flex justify-content-between text-success">
      <span>Discount (Coupon)</span>
      <span>- $<?php echo number_format($discount, 2); ?></span>
    </li>
  <?php endif; ?>

  <li class="list-group-item d-flex justify-content-between fw-bold">
    <span>Total</span>
    <span>$<?php echo number_format($totalAmount + $deliveryFee - $discount, 2); ?></span>
  </li>

  <?php if (!empty($couponMessage)): ?>
    <li class="list-group-item text-info text-center fw-semibold">
      <?php echo $couponMessage; ?>
    </li>
  <?php endif; ?>
</ul>

      </div>
    </div>
  </div>
</div>
</body>
</html>
