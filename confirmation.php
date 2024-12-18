<?php
session_start();
include('connection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location:index.php");
    exit;
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    header("Location: landing.php");
    exit;
}

$connection = new Connection();
$pdo = $connection->openConnection();

// Fetch order details
$stmt = $pdo->prepare("SELECT o.*, u.first_name, u.last_name FROM orders o 
                       INNER JOIN users u ON o.user_id = u.user_id 
                       WHERE o.order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order = $stmt->fetch();

$stmt = $pdo->prepare("SELECT oi.*, b.title FROM order_items oi 
                       INNER JOIN books b ON oi.book_id = b.book_id 
                       WHERE oi.order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5 text-center">
    <h1>Thank You for Your Order!</h1>
    <p>Your order #<?= $order['order_id'] ?> has been placed successfully.</p>
    <h3>Order Summary</h3>
    <ul class="list-group">
        <?php foreach ($order_items as $item): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><?= htmlspecialchars($item['title']) ?> x <?= $item['quantity'] ?></span>
                <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
            </li>
        <?php endforeach; ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <strong>Total:</strong>
            <strong>$<?= number_format($order['total_amount'], 2) ?></strong>
        </li>
    </ul>
</div>
</body>
</html>
