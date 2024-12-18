<?php
session_start();
include('connection.php');

// Ensure the admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$connection = new Connection();
$pdo = $connection->openConnection();

// Fetch messages
$stmt = $pdo->query("SELECT m.*, u.first_name, u.last_name FROM messages m 
                     INNER JOIN users u ON m.user_id = u.user_id 
                     ORDER BY m.date_sent DESC");
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <h2>User Messages</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>User</th>
                <th>Message</th>
                <th>Date Sent</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $message): ?>
                <tr>
                    <td><?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?></td>
                    <td><?= htmlspecialchars($message['message']) ?></td>
                    <td><?= htmlspecialchars($message['date_sent']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
