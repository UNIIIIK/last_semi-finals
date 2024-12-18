<?php
session_start();
include('connection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location:index.php");
    exit;
}

$connection = new Connection();
$pdo = $connection->openConnection();

$message_sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO messages (user_id, message) VALUES (:user_id, :message)");
    $stmt->execute(['user_id' => $user_id, 'message' => $message]);

    $message_sent = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Contact Us</h2>

    <?php if ($message_sent): ?>
        <div class="alert alert-success">Your message has been sent successfully.</div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="message" class="form-label">Your Message</label>
            <textarea name="message" id="message" class="form-control" rows="5" placeholder="Enter your message here..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
