<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - ShopZone Admin</title>
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
        
        .search-box {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
        <h2><i class="fas fa-users me-2"></i>Manage Users</h2>
        
        <div class="search-box">
            <form method="get" action="admin_users.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="search" placeholder="Search by username or email" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-success me-2" id="showAllBtn">Show All Users</button>
                            <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-body">
                <?php
                include 'connect.php';
                
                // Handle search or show all
                if (isset($_GET['search'])) {
                    $search = $conn->real_escape_string($_GET['search']);
                    $sql = "SELECT * FROM User WHERE username LIKE '%$search%' OR email LIKE '%$search%'";
                    $title = "Search Results for '$search'";
                } elseif (isset($_GET['show_all'])) {
                    $sql = "SELECT * FROM User";
                    $title = "All Users";
                } else {
                    $sql = "";
                    $title = "";
                }
                
                if (!empty($sql)) {
                    $result = $conn->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        echo "<h5 class='card-title'>$title</h5>";
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-striped'>";
                        echo "<thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                </tr>
                              </thead>";
                        echo "<tbody>";
                        
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>".htmlspecialchars($row['username'])."</td>
                                    <td>".htmlspecialchars($row['name'])."</td>
                                    <td>".htmlspecialchars($row['email'])."</td>
                                    <td>".htmlspecialchars($row['contact'])."</td>
                                    <td>".htmlspecialchars(substr($row['address'], 0, 20))."...</td>
                                  </tr>";
                        }
                        
                        echo "</tbody></table></div>";
                    } else {
                        echo "<div class='alert alert-info'>No users found</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show all users
        document.getElementById('showAllBtn').addEventListener('click', function() {
            window.location.href = 'admin_users.php?show_all=true';
        });
        
        // Reset display
        document.getElementById('resetBtn').addEventListener('click', function() {
            window.location.href = 'admin_users.php';
        });
    </script>
</body>
</html>