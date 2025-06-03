<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coupon_code = $conn->real_escape_string($_POST['coupon_code']);
    $discount = floatval($_POST['discount']);
    $starting_date = $conn->real_escape_string($_POST['starting_date']);
    $expiry_date = $conn->real_escape_string($_POST['expiry_date']);
    $minimum_amount = floatval($_POST['minimum_amount']);
    
    $sql = "INSERT INTO coupon (coupon_code, discount, starting_date, expiry_date, minimum_amount) 
            VALUES ('$coupon_code', $discount, '$starting_date', '$expiry_date', $minimum_amount)";
    
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
    <title>Add Coupon - ShopZone Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Same styling as admin_coupons.php */
    </style>
</head>
<body>
    <!-- Sidebar Navigation (same as admin_coupons.php) -->

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-plus me-2"></i>Add New Coupon</h2>
            <a href="admin_coupons.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Coupons
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Coupon Code</label>
                        <input type="text" class="form-control" name="coupon_code" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Discount Amount</label>
                        <input type="number" step="0.01" class="form-control" name="discount" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Minimum Order Amount</label>
                        <input type="number" step="0.01" class="form-control" name="minimum_amount" value="0.00">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Starting Date</label>
                            <input type="date" class="form-control" name="starting_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" name="expiry_date" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Coupon</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>