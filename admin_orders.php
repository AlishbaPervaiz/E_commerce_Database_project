<?php
include 'connect.php';

// Handle order status updates
if (isset($_GET['update_status'])) {
    $order_id = intval($_GET['order_id']);
    $new_status = $_GET['new_status'];
    
    // Validate status
    $valid_statuses = ['Confirmed', 'Shipped', 'Delivered'];
    if (!in_array($new_status, $valid_statuses)) {
        die("Invalid status");
    }
    
    // Update order status
    $conn->query("UPDATE `order` SET status = '$new_status' WHERE order_id = $order_id");
    
    // Handle shipping information for Shipped status
    if ($new_status === 'Shipped') {
        // Generate tracking number (PK + day + year (last 2 digits) + month + order_id (padded to 3 digits)
        $tracking_number = 'PK' . date('d') . date('y') . date('m') . str_pad($order_id, 3, '0', STR_PAD_LEFT);
        
        // Get shipping address from order
        $order = $conn->query("SELECT delivery_address FROM `order` WHERE order_id = $order_id")->fetch_assoc();
        $shipping_address = $order['delivery_address'];
        
        // Insert into shipping table
        $conn->query("INSERT INTO shipping (order_id, tracking_number, shipping_address, courier_company, shipping_date) 
                     VALUES ($order_id, '$tracking_number', '$shipping_address', 'TCS Express', CURDATE())");
    }
    
    // Update delivery date for Delivered status
    if ($new_status === 'Delivered') {
        $conn->query("UPDATE shipping SET delivery_date = CURDATE() WHERE order_id = $order_id");
    }
    
    header("Location: admin_orders.php");
    exit();
}

// Handle search
$search_query = '';
$where_clause = "WHERE o.status IN ('Pending', 'Confirmed', 'Shipped')";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause .= " AND o.order_id = '$search'";
    $search_query = '&search=' . urlencode($_GET['search']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Orders - ShopZone Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
            width: 250px;
            position: fixed;
        }
        .sidebar-header {
            padding: 20px;
            background: #1a252f;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: #495057;
            color: white;
        }
        .sidebar .nav-link.active {
            background: #007bff;
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
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
        .order-header {
            padding: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .order-body {
            padding: 15px;
        }
        .order-footer {
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid rgba(0,0,0,0.1);
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
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .action-btn {
            margin-right: 8px;
            min-width: 100px;
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
        .search-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}
    </style>
</head>
<body>
     <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="p-4">
            <h3 class="text-center mb-4">ShopZone Admin</h3>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="admin_users.php">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_products.php">
                        <i class="fas fa-box-open"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_orders.php">
                        <i class="fas fa-shopping-cart"></i> Current Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_order_history.php">
                        <i class="fas fa-history"></i> Orders History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_reviews.php">
                        <i class="fas fa-star"></i> Reviews
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_coupons.php">
                        <i class="fas fa-tag"></i> Coupons
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Search Bar -->
<div class="search-container mb-4">
    <form method="GET" action="admin_orders.php" class="row g-3">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by order ID..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
        <div class="col-md-4">
            <?php if (!empty($_GET['search'])): ?>
                <a href="admin_orders.php" class="btn btn-outline-danger">
                    <i class="fas fa-times"></i> Clear Search
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-shopping-cart me-2"></i>Current Orders</h2>
        </div>

        <?php
        // Get all orders with status Pending, Confirmed, or Shipped
        $orders = $conn->query("
    SELECT o.*, u.username 
    FROM `order` o
    LEFT JOIN user u ON o.username = u.username
    $where_clause
    ORDER BY 
        CASE o.status
            WHEN 'Pending' THEN 1
            WHEN 'Confirmed' THEN 2
            WHEN 'Shipped' THEN 3
        END,
        o.order_date ASC
");
        
        if ($orders->num_rows === 0) {
            echo '<div class="alert alert-info">No current orders found</div>';
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
                                <h6>Customer Information</h6>
                                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
                                <p class="mb-1"><strong>Username:</strong> <?= htmlspecialchars($order['username']) ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                                <p class="mb-1"><strong>Contact:</strong> <?= htmlspecialchars($order['contact']) ?></p>
                                 <?php if ($order['coupon_id']): ?>
                                 <?php 
                                 $coupon = $conn->query("SELECT coupon_code FROM coupon WHERE coupon_id = ".$order['coupon_id'])->fetch_assoc();
                                 if ($coupon): ?>
                                 <p class="mb-1"><strong>Coupon:</strong> 
                                 ID: <?= $order['coupon_id'] ?> | 
                                 Code: <?= htmlspecialchars($coupon['coupon_code']) ?>
                                </p>
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
                        
                        <div class="d-flex justify-content-end">
                            <div class="text-end">
                                <p class="mb-1"><strong>Subtotal:</strong> $<?= number_format($order['total_amount'] - $order['discount'], 2) ?></p>
                                <?php if ($order['discount'] > 0): ?>
                                    <p class="mb-1"><strong>Discount:</strong> -$<?= number_format($order['discount'], 2) ?></p>
                                <?php endif; ?>
                                <p class="mb-1"><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-footer d-flex justify-content-end">
                        <?php if ($status === 'Pending'): ?>
                            <a href="admin_orders.php?update_status=true&order_id=<?= $order_id ?>&new_status=Confirmed" class="btn btn-info action-btn">
                                <i class="fas fa-check-circle me-1"></i> Confirm
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($status === 'Pending' || $status === 'Confirmed'): ?>
                            <a href="admin_orders.php?update_status=true&order_id=<?= $order_id ?>&new_status=Shipped" class="btn btn-primary action-btn">
                                <i class="fas fa-truck me-1"></i> Ship
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($status === 'Shipped'): ?>
                            <a href="admin_orders.php?update_status=true&order_id=<?= $order_id ?>&new_status=Delivered" class="btn btn-success action-btn">
                                <i class="fas fa-check me-1"></i> Deliver
                            </a>
                        <?php endif; ?>
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