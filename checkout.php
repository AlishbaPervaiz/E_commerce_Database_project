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
        $quantity = $_SESSION['cart'][$productId];
        $subtotal = $quantity * $row['price'];
        $totalAmount += $subtotal;
        $row['quantity'] = $quantity;
        $row['subtotal'] = $subtotal;
        $cartItems[] = $row;
    }
}

// Set delivery fee and initialize variables
$deliveryFee = 50;
$discount = 0;
$couponMessage = "";
$coupon_id = null;
$now = date('Y-m-d'); // Current date for coupon validation
$appliedCouponCode = "";

// Handle coupon application
if (isset($_POST['apply_coupon'])) {
    $coupon_code = isset($_POST['coupon_code']) ? trim($conn->real_escape_string($_POST['coupon_code'])) : '';
    $appliedCouponCode = $coupon_code;
    
    if (!empty($coupon_code)) {
        $couponQuery = "SELECT * FROM coupon WHERE coupon_code = ? AND starting_date <= ? AND expiry_date >= ?";
        $stmt = $conn->prepare($couponQuery);
        $stmt->bind_param("sss", $coupon_code, $now, $now);
        $stmt->execute();
        $couponResult = $stmt->get_result();

        if ($couponResult->num_rows > 0) {
            $coupon = $couponResult->fetch_assoc();
            if ($totalAmount >= $coupon['minimum_amount']) {
                $discount = min($coupon['discount'], $totalAmount);
                $coupon_id = $coupon['coupon_id'];
                $couponMessage = "Coupon applied! You saved $".number_format($discount, 2).".";
                $_SESSION['applied_coupon'] = $coupon; // Store in session
            } else {
                $couponMessage = "Minimum order of $".number_format($coupon['minimum_amount'], 2)." required for this coupon.";
            }
        } else {
            $couponMessage = "Invalid or expired coupon code.";
        }
        $stmt->close();
    } else {
        $couponMessage = "Please enter a coupon code.";
    }
}

// Handle order submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_order'])) {
    $fullName = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $address = $conn->real_escape_string($_POST['delivery_address']);
    
    // Use coupon from session if applied
    if (isset($_SESSION['applied_coupon'])) {
        $coupon = $_SESSION['applied_coupon'];
        $discount = min($coupon['discount'], $totalAmount);
        $coupon_id = $coupon['coupon_id'];
    }

    // Calculate final amount
    $grandTotal = max(0, $totalAmount + $deliveryFee - $discount);
    $orderDate = date('Y-m-d H:i:s');
    $username = isset($_SESSION['username']) ? "'" . $conn->real_escape_string($_SESSION['username']) . "'" : "NULL";

    // Insert order with coupon_id
    $insertOrder = "INSERT INTO `order` (username, full_name, email, contact, delivery_address, status, total_amount, discount, order_date, coupon_id)
                    VALUES ($username, '$fullName', '$email', '$contact', '$address', 'Pending', $grandTotal, $discount, '$orderDate', " . ($coupon_id ? $coupon_id : "NULL") . ")";

    if ($conn->query($insertOrder)) {
        $order_id = $conn->insert_id; 

        // Insert order details
        foreach ($cartItems as $item) {
            $pid = $item['product_id'];
            $price = $item['price'];
            $quantity = $item['quantity'];
            $insertDetail = "INSERT INTO orderdetails (order_id, product_id, quantity, price)
                            VALUES ($order_id, $pid, $quantity, $price)";
            $conn->query($insertDetail);
        }

        // Clear cart and session data
        unset($_SESSION['cart']);
        unset($_SESSION['applied_coupon']);
        header("Location: order_confirmation.php");
        exit();
    } else {
        $couponMessage = "Error placing order: " . $conn->error;
    }
}

// Store form values from POST or initialize empty
$fullName = isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$contact = isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : '';
$address = isset($_POST['delivery_address']) ? htmlspecialchars($_POST['delivery_address']) : '';
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
    .coupon-success { color: #28a745; }
    .coupon-error { color: #dc3545; }
    .coupon-section { display: flex; gap: 10px; }
    .coupon-section input { flex: 1; }
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
          <input type="text" class="form-control" name="full_name" value="<?php echo $fullName; ?>" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="<?php echo $email; ?>" required>
        </div>
        <div class="mb-3">
          <label for="contact" class="form-label">Contact</label>
          <input type="text" class="form-control" name="contact" value="<?php echo $contact; ?>" required>
        </div>
        <div class="mb-3">
          <label for="delivery_address" class="form-label">Delivery Address</label>
          <textarea class="form-control" name="delivery_address" rows="3" required><?php echo $address; ?></textarea>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Coupon Code</label>
          <div class="coupon-section">
            <input type="text" class="form-control" name="coupon_code" value="<?php echo htmlspecialchars($appliedCouponCode); ?>" placeholder="Enter coupon code">
            <button type="submit" name="apply_coupon" class="btn btn-outline-primary">Apply</button>
          </div>
          <?php if (!empty($couponMessage)): ?>
            <div class="mt-2 <?php echo ($discount > 0) ? 'coupon-success' : 'coupon-error'; ?>">
              <?php echo htmlspecialchars($couponMessage); ?>
            </div>
          <?php endif; ?>
        </div>
        
        <button type="submit" name="confirm_order" class="btn btn-success w-100">Confirm Order</button>
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
                <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
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
        </ul>
      </div>
    </div>
  </div>
</div>
</body>
</html>