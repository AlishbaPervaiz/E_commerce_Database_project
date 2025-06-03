<?php
include 'connect.php';

// Handle search
$search_query = '';
$where_clause = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause = "WHERE (r.order_id = '$search' OR r.username LIKE '%$search%' OR r.review_id = '$search')";
    $search_query = '&search=' . urlencode($_GET['search']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews - ShopZone Admin</title>
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
        .review-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 5px solid #6f42c1;
        }
        .review-header {
            padding: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .review-body {
            padding: 15px;
        }
        .rating-stars {
            color: #ffc107;
            font-size: 1.2rem;
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
                    <a class="nav-link active" href="admin_reviews.php">
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
            <form method="GET" action="admin_reviews.php" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search by Order ID, Username or Review ID..." 
                               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <?php if (!empty($_GET['search'])): ?>
                        <a href="admin_reviews.php" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> Clear Search
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-star me-2"></i>Customer Reviews</h2>
        </div>

        <?php
        $reviews = $conn->query("
            SELECT r.*, o.order_date 
            FROM review r
            JOIN `order` o ON r.order_id = o.order_id
            $where_clause
            ORDER BY r.review_id DESC
        ");
        
        if ($reviews->num_rows === 0) {
            echo '<div class="alert alert-info">No reviews found</div>';
        } else {
            while ($review = $reviews->fetch_assoc()) {
                ?>
                <div class="card review-card">
                    <div class="review-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Review #<?= $review['review_id'] ?></h5>
                            <small class="text-muted">Order #<?= $review['order_id'] ?> • <?= date('M d, Y', strtotime($review['order_date'])) ?></small>
                        </div>
                        <div>
                            <span class="badge bg-primary">
                                <?= $review['username'] ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="review-body">
                        <div class="mb-3">
                            <span class="rating-stars">
                                <?php 
                                $full_stars = $review['rating'];
                                $empty_stars = 5 - $full_stars;
                                for ($i = 0; $i < $full_stars; $i++) {
                                    echo '<i class="fas fa-star"></i>';
                                }
                                for ($i = 0; $i < $empty_stars; $i++) {
                                    echo '<i class="far fa-star"></i>';
                                }
                                ?>
                                <span class="ms-2"><?= $review['rating'] ?>/5</span>
                            </span>
                        </div>
                        
                        <?php if (!empty($review['comment'])): ?>
                            <div class="mb-3">
                                <h6>Customer Comment:</h6>
                                <p class="mb-0"><?= htmlspecialchars($review['comment']) ?></p>
                            </div>
                        <?php endif; ?>
                        
                       
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(reviewId) {
            if (confirm('Are you sure you want to delete this review?')) {
                window.location.href = 'delete_review.php?id=' + reviewId;
            }
        }
    </script>
</body>
</html>