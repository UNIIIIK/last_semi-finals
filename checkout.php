<?php
session_start();
include('connection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location:index.php");
    exit;
}

$connection = new Connection();
$pdo = $connection->openConnection();

// Fetch cart items for the user
$stmt = $pdo->prepare("SELECT c.*, b.title, b.price, b.image FROM cart c 
                       INNER JOIN books b ON c.book_id = b.book_id 
                       WHERE c.user_id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Calculate total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Handle checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $user_id = $_SESSION['user_id'];
    $shipping_address = $_POST['shipping_address'];
    $payment_method = $_POST['payment_method'];

    // Save the order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (:user_id, :total_amount, 'pending')");
    $stmt->execute(['user_id' => $user_id, 'total_amount' => $total_amount]);
    $order_id = $pdo->lastInsertId();

    // Save the order items
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) 
                               VALUES (:order_id, :book_id, :quantity, :price)");
        $stmt->execute([
            'order_id' => $order_id,
            'book_id' => $item['book_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
        ]);
    }

    // Clear the user's cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);

    // Redirect to a confirmation page
    header("Location: confirmation.php?order_id=$order_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Checkout</h2>

    <!-- Display cart items -->
    <div class="row">
        <div class="col-md-8">
            <h3>Order Summary</h3>
            <ul class="list-group mb-4">
                <?php foreach ($cart_items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5><?= htmlspecialchars($item['title']) ?></h5>
                            <small>$<?= number_format($item['price'], 2) ?> x <?= $item['quantity'] ?></small>
                        </div>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </li>
                <?php endforeach; ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Total:</strong>
                    <strong>$<?= number_format($total_amount, 2) ?></strong>
                </li>
            </ul>
        </div>

        <!-- Checkout form -->
        <div class="col-md-4">
            <h3>Shipping Information</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="shipping_address" class="form-label">Shipping Address</label>
                    <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="credit_card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="cod">Cash on Delivery</option>
                    </select>
                </div>
                <button type="submit" name="checkout" class="btn btn-primary w-100">Place Order</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
