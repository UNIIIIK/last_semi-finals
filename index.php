<?php
session_start();
include('connection.php');

$connection = new Connection();
$pdo = $connection->openConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check user in the `users` table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];

            if ($user['role'] === 'admin') {
                header("Location:admin.php");
            } else {
                header("Location:landing.php");
            }
            exit;
        } else {
            $error = "Incorrect email or password.";
        }
    } elseif (isset($_POST['register'])) {
        // Handle user registration
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $contact_number = $_POST['contact_number'];
        $address = $_POST['address'];

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            $error = "Email is already registered.";
        } else {
            // Hash password and save user to database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, contact_number, address, role) 
                                   VALUES (:first_name, :last_name, :email, :password, :contact_number, :address, 'user')");
            $stmt->execute([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password' => $hashed_password,
                'contact_number' => $contact_number,
                'address' => $address
            ]);
            $success = "Registration successful. You can now log in.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Master's Bookstore</h2>
    
    <!-- Display Success/Error Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form action="index.php" method="POST" class="mt-3">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
    </form>

    <!-- Register Button -->
    <button type="button" class="btn btn-secondary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>

    <!-- Registration Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="index.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Register</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" placeholder="Enter your first name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" placeholder="Enter your last name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" placeholder="Enter your contact number" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Enter your address" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="register" class="btn btn-success w-100">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
