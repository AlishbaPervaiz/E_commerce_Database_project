<?php
include 'connect.php';

// Handle stock status change
if (isset($_GET['toggle_stock'])) {
    $product_id = intval($_GET['toggle_stock']);
    $current_status = $conn->query("SELECT is_available FROM product WHERE product_id = $product_id")->fetch_assoc()['is_available'];
    $new_status = $current_status ? 0 : 1;
    $conn->query("UPDATE product SET is_available = $new_status WHERE product_id = $product_id");
    header("Location: admin_products.php");
    exit();
}

// Handle product deletion
if (isset($_GET['delete_product'])) {
    $product_id = intval($_GET['delete_product']);
    $conn->query("DELETE FROM product WHERE product_id = $product_id");
    header("Location: admin_products.php");
    exit();
}

// Handle search
$search_query = '';
$where_clause = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause = "WHERE (name LIKE '%$search%' OR product_id = '$search')";
    $search_query = '&search=' . urlencode($_GET['search']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - ShopZone Admin</title>
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
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .product-img {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
        }
        .out-of-stock {
            opacity: 0.6;
            position: relative;
        }
        .out-of-stock::after {
            content: "Out of Stock";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            text-align: center;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px;
            transform: translateY(-50%);
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
    <div class="sidebar">
        <div class="p-4">
            <h3 class="text-center mb-4">ShopZone Admin</h3>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="admin_users.php">
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
    <form method="GET" action="admin_products.php" class="row g-3">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by product name or ID..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
        <div class="col-md-4">
            <?php if (!empty($_GET['search'])): ?>
                <a href="admin_products.php" class="btn btn-outline-danger">
                    <i class="fas fa-times"></i> Clear Search
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-box-open me-2"></i>Manage Products</h2>
            <a href="admin_add_product.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Product
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM product $where_clause ORDER BY product_id DESC");
                            while ($row = $result->fetch_assoc()):
                                $is_out_of_stock = !$row['is_available'];
                            ?>
                            <tr class="<?= $is_out_of_stock ? 'out-of-stock' : '' ?>">
                                <td>
                                    <?php if ($row['product_image_url']): ?>
                                        <img src="<?= htmlspecialchars($row['product_image_url']) ?>" class="product-img" alt="<?= htmlspecialchars($row['name']) ?>">
                                    <?php else: ?>
                                        <div class="text-center py-3 bg-light">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td>$<?= number_format($row['price'], 2) ?></td>
                                <td><?= htmlspecialchars($row['product_category']) ?></td>
                                <td>
                                    <span class="badge <?= $is_out_of_stock ? 'bg-secondary' : 'bg-success' ?>">
                                        <?= $is_out_of_stock ? 'Out of Stock' : 'In Stock' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="admin_edit_product.php?id=<?= $row['product_id'] ?>" class="btn btn-sm btn-warning me-2">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button onclick="confirmDelete(<?= $row['product_id'] ?>)" class="btn btn-sm btn-danger me-2">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        <a href="admin_products.php?toggle_stock=<?= $row['product_id'] ?>" class="btn btn-sm <?= $is_out_of_stock ? 'btn-success' : 'btn-info' ?>">
                                            <i class="fas <?= $is_out_of_stock ? 'fa-check' : 'fa-times' ?>"></i>
                                            <?= $is_out_of_stock ? 'Mark In Stock' : 'Mark Out of Stock' ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                window.location.href = 'admin_products.php?delete_product=' + productId;
            }
        }
    </script>
</body>
</html>