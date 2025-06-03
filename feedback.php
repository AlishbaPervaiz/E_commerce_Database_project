<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Verify the order belongs to the user and is delivered
$order_check = $conn->query("
    SELECT * FROM `order` 
    WHERE order_id = $order_id 
    AND username = '$username' 
    AND status = 'Delivered'
");

if ($order_check->num_rows === 0) {
    die("Invalid order or order not delivered yet");
}

// Check if feedback already exists
$feedback_exists = $conn->query("
    SELECT * FROM review 
    WHERE order_id = $order_id
")->num_rows > 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$feedback_exists) {
    $rating = intval($_POST['rating']);
    $comment = $conn->real_escape_string($_POST['comment']);
    
    // Validate rating (1-5)
    if ($rating < 1 || $rating > 5) {
        $error = "Please select a rating between 1 and 5 stars";
    } else {
        // Insert feedback
        $conn->query("
            INSERT INTO review (username, order_id, rating, comment)
            VALUES ('$username', $order_id, $rating, '$comment')
        ");
        
        header("Location: order_history.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Feedback - ShopZone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .feedback-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .rating-stars {
            font-size: 2rem;
            color: #ffc107;
            margin: 1rem 0;
        }
        .star {
            cursor: pointer;
            transition: all 0.2s;
        }
        .star:hover {
            transform: scale(1.2);
        }
        .star.active {
            color: #fd7e14;
        }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="feedback-container">
            <h2 class="text-center mb-4">
                <i class="fas fa-comment-alt"></i> Leave Feedback for Order #<?= $order_id ?>
            </h2>
            
            <?php if ($feedback_exists): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-check-circle"></i> You've already submitted feedback for this order.
                    <a href="order_history.php" class="btn btn-sm btn-outline-primary ms-2">
                        View Order History
                    </a>
                </div>
            <?php else: ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-4 text-center">
                        <h5>How would you rate your experience?</h5>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="far fa-star star" data-rating="<?= $i ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="rating" value="0" required>
                        <small class="text-muted">Click to rate (1-5 stars)</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="comment" class="form-label">Your Comments (Optional)</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" 
                                  placeholder="Share your experience with this order..."></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Submit Feedback
                        </button>
                        <a href="order_history.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Orders
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Star rating interaction
        document.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                document.getElementById('rating').value = rating;
                
                // Update star display
                document.querySelectorAll('.star').forEach((s, i) => {
                    if (i < rating) {
                        s.classList.remove('far');
                        s.classList.add('fas', 'active');
                    } else {
                        s.classList.remove('fas', 'active');
                        s.classList.add('far');
                    }
                });
            });
        });
    </script>
</body>
</html>