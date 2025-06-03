<?php
session_start();
include 'connect.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['product_id'])) {
    header('Content-Type: application/json');

    $action = $_POST['action'];
    $productId = intval($_POST['product_id']);
    $response = ['success' => false];

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if ($productId > 0 && in_array($action, ['increase', 'decrease', 'remove'])) {
        switch ($action) {
            case 'increase':
                $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + 1;
                break;

            case 'decrease':
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]--;
                    if ($_SESSION['cart'][$productId] <= 0) {
                        unset($_SESSION['cart'][$productId]);
                    }
                }
                break;

            case 'remove':
                unset($_SESSION['cart'][$productId]);
                break;
        }

        $response['success'] = true;
        $response['cartCount'] = array_sum($_SESSION['cart']);
    }

    echo json_encode($response);
    exit;
}

// Page rendering logic
$cart = $_SESSION['cart'] ?? [];
$productDetails = [];
$totalPrice = 0;

if (!empty($cart)) {
    $ids = implode(",", array_map('intval', array_keys($cart)));
    $sql = "SELECT * FROM Product WHERE product_id IN ($ids)";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pid = $row['product_id'];
            $qty = $cart[$pid];
            $row['quantity'] = $qty;
            $row['subtotal'] = $qty * $row['price'];
            $productDetails[] = $row;
            $totalPrice += $row['subtotal'];
        }
    }
}

$cartCount = array_sum($cart);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Your Cart - ShopZone</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .cart-table img { width: 80px; height: auto; border-radius: 5px; }
    .cart-table th, .cart-table td { vertical-align: middle !important; }
    footer { background-color: #333; color: white; padding: 30px 0; text-align: center; margin-top: 50px; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
  <a class="navbar-brand" href="index.php">ShopZone</a>
  <div class="ms-auto d-flex align-items-center">
    <span class="me-3">Cart Items: <span id="cart-count"><?php echo $cartCount; ?></span></span>
    <a href="product.php" class="btn btn-outline-primary me-2">Continue Shopping</a>
  </div>
</nav>

<div class="container mt-5">
  <h2 class="mb-4 text-center">Your Shopping Cart</h2>

  <?php if (!empty($productDetails)): ?>
    <div class="table-responsive">
      <table class="table cart-table table-bordered align-middle">
        <thead class="table-secondary">
          <tr>
            <th>Product</th>
            <th>Thumbnail</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="cart-body">
          <?php foreach ($productDetails as $product): ?>
            <tr data-product-id="<?php echo $product['product_id']; ?>">
              <td><?php echo htmlspecialchars($product['name']); ?></td>
              <td><img src="<?php echo $product['product_image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"></td>
              <td>$<?php echo number_format($product['price'], 2); ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <button class="btn btn-sm btn-outline-dark qty-btn" data-action="decrease">−</button>
                  <span class="qty-display"><?php echo $product['quantity']; ?></span>
                  <button class="btn btn-sm btn-outline-dark qty-btn" data-action="increase">+</button>
                </div>
              </td>
              <td class="subtotal">$<?php echo number_format($product['subtotal'], 2); ?></td>
              <td>
                <button class="btn btn-outline-danger btn-sm remove-btn">✕</button>
              </td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="4" class="text-end"><strong>Total:</strong></td>
            <td colspan="2" id="total-price"><strong>$<?php echo number_format($totalPrice, 2); ?></strong></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="text-end mt-3">
      <a href="checkout.php" class="btn btn-success btn-lg">Proceed to Checkout</a>
    </div>
  <?php else: ?>
    <p class="text-center">Your cart is empty. <a href="product.php">Start shopping</a>.</p>
  <?php endif; ?>
</div>

<footer>
  <p>&copy; 2025 ShopZone. All rights reserved.</p>
</footer>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const cartBody = document.getElementById("cart-body");
  const cartCountSpan = document.getElementById("cart-count");
  const totalPriceCell = document.getElementById("total-price");

  const formatCurrency = val => "$" + parseFloat(val).toFixed(2);

  const recalculateTotal = () => {
    let total = 0;
    document.querySelectorAll("#cart-body tr[data-product-id]").forEach(row => {
      const subtotalText = row.querySelector(".subtotal").textContent.replace("$", "");
      total += parseFloat(subtotalText);
    });
    totalPriceCell.innerHTML = "<strong>" + formatCurrency(total) + "</strong>";
  };

  const updateRow = (row, qty, price) => {
    row.querySelector(".qty-display").textContent = qty;
    const subtotal = qty * price;
    row.querySelector(".subtotal").textContent = formatCurrency(subtotal);
    recalculateTotal();
  };

  cartBody.addEventListener("click", function (e) {
    const btn = e.target.closest("button");
    if (!btn) return;

    const row = btn.closest("tr");
    const productId = row.getAttribute("data-product-id");
    const action = btn.classList.contains("qty-btn") 
                   ? btn.getAttribute("data-action") 
                   : (btn.classList.contains("remove-btn") ? "remove" : "");

    if (!action) return;

    fetch("cart.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `action=${action}&product_id=${productId}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        cartCountSpan.textContent = data.cartCount;

        if (action === "remove") {
          row.remove();
        } else {
          const price = parseFloat(row.querySelector("td:nth-child(3)").textContent.replace("$", ""));
          let qty = parseInt(row.querySelector(".qty-display").textContent);
          qty = action === "increase" ? qty + 1 : qty - 1;

          if (qty <= 0) {
            row.remove();
          } else {
            updateRow(row, qty, price);
          }
        }

        if (document.querySelectorAll("#cart-body tr[data-product-id]").length === 0) {
          document.querySelector(".table-responsive").remove();
          document.querySelector(".text-end.mt-3").remove();
          const emptyMsg = document.createElement("p");
          emptyMsg.className = "text-center";
          emptyMsg.innerHTML = 'Your cart is empty. <a href="product.php">Start shopping</a>.';
          document.querySelector(".container").appendChild(emptyMsg);
        }
      }
    });
  });
});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>