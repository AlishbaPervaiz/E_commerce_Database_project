<?php
include 'connect.php';

// Handle coupon status toggle
if (isset($_GET['toggle_status'])) {
    $coupon_id = intval($_GET['coupon_id']);
    $current_date = date('Y-m-d');
    
    // Toggle expiry date between current date (unavailable) and future date (available)
    $coupon = $conn->query("SELECT expiry_date FROM coupon WHERE coupon_id = $coupon_id")->fetch_assoc();
    $new_expiry = ($coupon['expiry_date'] <= $current_date) ? date('Y-m-d', strtotime('+1 month')) : $current_date;
    
    $conn->query("UPDATE coupon SET expiry_date = '$new_expiry' WHERE coupon_id = $coupon_id");
    header("Location: admin_coupons.php");
    exit();
}

// Handle coupon deletion
if (isset($_GET['delete_coupon'])) {
    $coupon_id = intval($_GET['coupon_id']);
    $conn->query("DELETE FROM coupon WHERE coupon_id = $coupon_id");
    header("Location: admin_coupons.php");
    exit();
}

// Handle search
$search_query = '';
$where_clause = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause = "WHERE (coupon_code LIKE '%$search%' OR coupon_id = '$search')";
    $search_query = '&search=' . urlencode($_GET['search']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coupons - ShopZone Admin</title>
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
        .coupon-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 5px solid;
        }
        .coupon-card.active {
            border-left-color: #28a745;
        }
        .coupon-card.expired {
            border-left-color: #dc3545;
            opacity: 0.8;
        }
        .coupon-header {
            padding: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .coupon-body {
            padding: 15px;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .badge-active {
            background-color: #28a745;
            color: white;
        }
        .badge-expired {
            background-color: #dc3545;
            color: white;
        }
        .search-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}
        .action-btn {
            margin-right: 8px;
            min-width: 120px;
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
    <form method="GET" action="admin_coupons.php" class="row g-3">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by coupon code or ID..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
        <div class="col-md-4">
            <?php if (!empty($_GET['search'])): ?>
                <a href="admin_coupons.php" class="btn btn-outline-danger">
                    <i class="fas fa-times"></i> Clear Search
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-tag me-2"></i>Manage Coupons</h2>
            <a href="admin_add_coupon.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Coupon
            </a>
        </div>

        <div class="row">
            <?php
            $current_date = date('Y-m-d');
            $coupons = $conn->query("SELECT * FROM coupon $where_clause ORDER BY expiry_date DESC");
            
            if ($coupons->num_rows === 0) {
                echo '<div class="alert alert-info">No coupons found</div>';
            } else {
                while ($coupon = $coupons->fetch_assoc()) {
                    $is_active = $coupon['expiry_date'] >= $current_date;
                    ?>
                    <div class="col-md-6">
                        <div class="card coupon-card <?= $is_active ? 'active' : 'expired' ?>">
                            <div class="coupon-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0"><?= htmlspecialchars($coupon['coupon_code']) ?></h5>
                                    <small class="text-muted">ID: <?= $coupon['coupon_id'] ?></small>
                                </div>
                                <div>
                                    <span class="status-badge badge-<?= $is_active ? 'active' : 'expired' ?>">
                                        <?= $is_active ? 'Active' : 'Expired' ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="coupon-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Discount:</strong> $<?= number_format($coupon['discount'], 2) ?></p>
                                        <p class="mb-1"><strong>Min. Amount:</strong> $<?= number_format($coupon['minimum_amount'], 2) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Start Date:</strong> <?= date('M d, Y', strtotime($coupon['starting_date'])) ?></p>
                                        <p class="mb-1"><strong>Expiry Date:</strong> <?= date('M d, Y', strtotime($coupon['expiry_date'])) ?></p>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <a href="admin_edit_coupon.php?id=<?= $coupon['coupon_id'] ?>" class="btn btn-warning action-btn">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <button onclick="confirmDelete(<?= $coupon['coupon_id'] ?>)" class="btn btn-danger action-btn">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(couponId) {
            if (confirm('Are you sure you want to delete this coupon?')) {
                window.location.href = 'admin_coupons.php?delete_coupon=' + couponId;
            }
        }
    </script>
</body>
</html>