<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - ShopZone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .order-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 5px solid;
        }
        .order-card.pending {
            border-left-color: #ffc107;
        }
        .order-card.confirmed {
            border-left-color: #17a2b8;
        }
        .order-card.shipped {
            border-left-color: #007bff;
        }
        .order-card.delivered {
            border-left-color: #28a745;
        }
        .order-header {
            padding: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .order-body {
            padding: 15px;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-confirmed {
            background-color: #17a2b8;
            color: white;
        }
        .badge-shipped {
            background-color: #007bff;
            color: white;
        }
        .badge-delivered {
            background-color: #28a745;
            color: white;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .quantity-badge {
            background-color: #6c757d;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            margin-left: 5px;
        }
        .feedback-btn {
            background-color: #6f42c1;
            color: white;
        }
        .feedback-btn:hover {
            background-color: #5a32a3;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-history me-2"></i>Order History</h2>
            <a href="product.php" class="btn btn-outline-primary">
                <i class="fas fa-shopping-bag me-1"></i> Continue Shopping
            </a>
        </div>

        <?php
        // Get all orders for the logged-in user
        $orders = $conn->query("
            SELECT * FROM `order` 
            WHERE username = '$username'
            ORDER BY 
                CASE status
                    WHEN 'Pending' THEN 1
                    WHEN 'Confirmed' THEN 2
                    WHEN 'Shipped' THEN 3
                    WHEN 'Delivered' THEN 4
                END,
                order_date DESC
        ");
        
        if ($orders->num_rows === 0) {
            echo '<div class="alert alert-info">You have no order history yet.</div>';
        } else {
            while ($order = $orders->fetch_assoc()) {
                $order_id = $order['order_id'];
                $status = $order['status'];
                
                // Get order details with product information
                $order_items = $conn->query("
                    SELECT od.*, p.name as product_name, p.product_image_url, od.quantity 
                    FROM orderdetails od
                    JOIN product p ON od.product_id = p.product_id
                    WHERE od.order_id = $order_id
                ");
                
                // Get shipping info if exists
                $shipping_info = $conn->query("
                    SELECT * FROM shipping WHERE order_id = $order_id
                ")->fetch_assoc();
                
                // Check if feedback already given
                $feedback_exists = $conn->query("
                    SELECT * FROM review WHERE order_id = $order_id
                ")->num_rows > 0;
                ?>
                
                <div class="card order-card <?= strtolower($status) ?>">
                    <div class="order-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Order #<?= $order_id ?></h5>
                            <small class="text-muted">Order Date: <?= date('M d, Y h:i A', strtotime($order['order_date'])) ?></small>
                        </div>
                        <div>
                            <span class="status-badge badge-<?= strtolower($status) ?>">
                                <?= $status ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="order-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>Order Information</h6>
                                <p class="mb-1"><strong>Status:</strong> <?= $status ?></p>
                                <p class="mb-1"><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
                                <?php if ($order['coupon_id']): ?>
                                    <?php 
                                    $coupon = $conn->query("SELECT coupon_code FROM coupon WHERE coupon_id = ".$order['coupon_id'])->fetch_assoc();
                                    if ($coupon): ?>
                                        <p class="mb-1"><strong>Coupon Used:</strong> <?= htmlspecialchars($coupon['coupon_code']) ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h6>Delivery Information</h6>
                                <p><strong>Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
                                <?php if ($shipping_info): ?>
                                    <p><strong>Courier:</strong> <?= $shipping_info['courier_company'] ?></p>
                                    <p><strong>Tracking #:</strong> <?= $shipping_info['tracking_number'] ?></p>
                                    <p><strong>Shipping Date:</strong> <?= date('M d, Y', strtotime($shipping_info['shipping_date'])) ?></p>
                                    <?php if ($shipping_info['delivery_date']): ?>
                                        <p><strong>Delivery Date:</strong> <?= date('M d, Y', strtotime($shipping_info['delivery_date'])) ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <h6>Order Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($item = $order_items->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td>$<?= number_format($item['price'], 2) ?></td>
                                        <td>
                                            <span class="quantity-badge"><?= $item['quantity'] ?></span>
                                        </td>
                                        <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                        <td>
                                            <?php if ($item['product_image_url']): ?>
                                                <img src="<?= htmlspecialchars($item['product_image_url']) ?>" class="product-img" alt="Product Image">
                                            <?php else: ?>
                                                <div class="product-img bg-light text-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-end">
                                <p class="mb-1"><strong>Subtotal:</strong> $<?= number_format($order['total_amount'] - $order['discount'], 2) ?></p>
                                <?php if ($order['discount'] > 0): ?>
                                    <p class="mb-1"><strong>Discount:</strong> -$<?= number_format($order['discount'], 2) ?></p>
                                <?php endif; ?>
                                <h5 class="mb-0"><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></h5>
                            </div>
                            
                            <?php if ($status === 'Delivered'): ?>
                                <div>
                                    <?php if ($feedback_exists): ?>
                                        <button class="btn btn-success" disabled>
                                            <i class="fas fa-check-circle me-1"></i> Feedback Submitted
                                        </button>
                                    <?php else: ?>
                                        <a href="feedback.php?order_id=<?= $order_id ?>" class="btn feedback-btn">
                                            <i class="fas fa-comment-alt me-1"></i> Give Feedback
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>