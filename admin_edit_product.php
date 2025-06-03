<?php
include 'connect.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $conn->query("SELECT * FROM product WHERE product_id = $product_id")->fetch_assoc();

if (!$product) {
    header("Location: admin_products.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $description = $conn->real_escape_string($_POST['description']);
    
    $sql = "UPDATE product SET 
            name = '$name', 
            price = $price, 
            product_category = '$category', 
            product_image_url = '$image_url', 
            description = '$description' 
            WHERE product_id = $product_id";
    
    if ($conn->query($sql)) {
        header("Location: admin_products.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
    /* Main Structure */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        color: #333;
    }
    
    .main-content {
        margin-left: 250px;
        padding: 30px;
    }
    
    /* Card Styling */
    .product-form-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .card-header {
        background-color: #2c3e50;
        color: white;
        padding: 20px;
        border-bottom: none;
    }
    
    .card-header h3 {
        margin: 0;
        font-weight: 600;
    }
    
    .card-body {
        padding: 30px;
    }
    
    /* Form Elements */
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .form-control {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 12px 15px;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: #2c3e50;
        box-shadow: 0 0 0 0.25rem rgba(44, 62, 80, 0.15);
    }
    
    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }
    
    /* Buttons */
    .btn-submit {
        background-color: #2c3e50;
        border: none;
        padding: 12px 25px;
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: all 0.3s;
    }
    
    .btn-submit:hover {
        background-color: #1a252f;
        transform: translateY(-2px);
    }
    
    .btn-back {
        background-color: #6c757d;
        border: none;
        padding: 12px 25px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-back:hover {
        background-color: #5a6268;
    }
    
    /* Header Section */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eaeaea;
    }
    
    .page-header h2 {
        color: #2c3e50;
        font-weight: 600;
        margin: 0;
    }
    
    /* Image Preview */
    .image-preview-container {
        margin-top: 20px;
        text-align: center;
    }
    
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        border: 2px dashed #ddd;
        padding: 10px;
        margin-bottom: 15px;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 20px;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .btn-back {
            margin-top: 15px;
        }
    }
    
    /* Form Groups */
    .form-group {
        margin-bottom: 25px;
    }
    
    /* Price Input */
    .price-input {
        position: relative;
    }
    
    .price-input:before {
        content: "$";
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: 500;
        color: #495057;
    }
    
    .price-input input {
        padding-left: 30px;
    }
    
    /* Status Message */
    .status-message {
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 25px;
        display: none;
    }
    
    .status-message.success {
        background-color: #d4edda;
        color: #155724;
        display: block;
    }
    
    .status-message.error {
        background-color: #f8d7da;
        color: #721c24;
        display: block;
    }
</style>
</head>
<body>
    <!-- Same sidebar as admin_products.php -->

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit me-2"></i>Edit Product</h2>
            <a href="admin_products.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" name="category" value="<?= htmlspecialchars($product['product_category']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image URL</label>
                        <input type="text" class="form-control" name="image_url" value="<?= htmlspecialchars($product['product_image_url']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>