<?php
session_start();
include('connection.php');

// Ensure the admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location:index.php");
    exit;
}

$connection = new Connection();
$pdo = $connection->openConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Master's Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="categories.php" class="nav-link">Categories</a></li>
                <li class="nav-item"><a href="books.php" class="nav-link">Books</a></li>
                <li class="nav-item"><a href="sales.php" class="nav-link">Sales Reports</a></li>
                <li class="nav-item"><a href="messages.php" class="nav-link">Messages</a></li>
                <li class="nav-item"><a href="index.php" class="nav-link text-danger">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1>Welcome, master's!</h1>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Categories</h5>
                    <p class="card-text">Manage product categories.</p>
                    <a href="categories.php" class="btn btn-primary">Manage Categories</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Books</h5>
                    <p class="card-text">Manage books in inventory.</p>
                    <a href="books.php" class="btn btn-primary">Manage Books</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Sales Reports</h5>
                    <p class="card-text">View and generate sales reports.</p>
                    <a href="sales.php" class="btn btn-primary">View Reports</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Messages</h5>
                    <p class="card-text">View messages from users.</p>
                    <a href="messages.php" class="btn btn-primary">View Messages</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Out of Stock</h5>
                    <p class="card-text">View books that are out of stock.</p>
                    <a href="books.php?filter=out_of_stock" class="btn btn-danger">View Out of Stock</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
