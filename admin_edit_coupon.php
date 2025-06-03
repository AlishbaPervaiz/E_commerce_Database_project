<?php
include 'connect.php';

// Get coupon ID from URL
$coupon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch coupon data
$coupon = $conn->query("SELECT * FROM coupon WHERE coupon_id = $coupon_id")->fetch_assoc();

if (!$coupon) {
    header("Location: admin_coupons.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coupon_code = $conn->real_escape_string($_POST['coupon_code']);
    $discount = floatval($_POST['discount']);
    $starting_date = $conn->real_escape_string($_POST['starting_date']);
    $expiry_date = $conn->real_escape_string($_POST['expiry_date']);
    $minimum_amount = floatval($_POST['minimum_amount']);

    $sql = "UPDATE coupon SET 
            coupon_code = '$coupon_code',
            discount = $discount,
            starting_date = '$starting_date',
            expiry_date = '$expiry_date',
            minimum_amount = $minimum_amount
            WHERE coupon_id = $coupon_id";

    if ($conn->query($sql)) {
        header("Location: admin_coupons.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Coupon - ShopZone Admin</title>
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
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .coupon-form-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .coupon-form-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
        }
        .card-header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 20px;
            border-bottom: none;
            border-radius: 10px 10px 0 0 !important;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-update {
            background-color: #2c3e50;
            color: white;
        }
        .btn-update:hover {
            background-color: #1a252f;
            color: white;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        .btn-back:hover {
            background-color: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-tag me-2"></i>Edit Coupon</h2>
            <a href="admin_coupons.php" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>Back to Coupons
            </a>
        </div>

        <div class="coupon-form-container">
            <div class="card coupon-form-card">
                <div class="card-header">
                    <h4 class="mb-0">Coupon Details</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="coupon_code" class="form-label">Coupon Code</label>
                            <input type="text" class="form-control" id="coupon_code" name="coupon_code" 
                                   value="<?= htmlspecialchars($coupon['coupon_code']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="discount" class="form-label">Discount Amount</label>
                            <input type="number" step="0.01" class="form-control" id="discount" name="discount" 
                                   value="<?= htmlspecialchars($coupon['discount']) ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="starting_date" class="form-label">Starting Date</label>
                                <input type="date" class="form-control" id="starting_date" name="starting_date" 
                                       value="<?= htmlspecialchars($coupon['starting_date']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date" 
                                       value="<?= htmlspecialchars($coupon['expiry_date']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="minimum_amount" class="form-label">Minimum Order Amount</label>
                            <input type="number" step="0.01" class="form-control" id="minimum_amount" name="minimum_amount" 
                                   value="<?= htmlspecialchars($coupon['minimum_amount']) ?>">
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-update">
                                <i class="fas fa-save me-2"></i>Update Coupon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>