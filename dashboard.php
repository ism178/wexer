<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch customers
$stmt = $pdo->prepare("SELECT * FROM customers WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$customers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Customer Dashboard</h2>
    <div class="d-flex justify-content-between mb-3">
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
    <form method="post" action="add_customer.php" class="card p-4 shadow-sm mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Customer Name" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="address" class="form-control" placeholder="Address">
            </div>
            <div class="col-12">
                <textarea name="notes" class="form-control" placeholder="Notes"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">Add Customer</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Notes</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['phone']) ?></td>
                <td><?= htmlspecialchars($c['address']) ?></td>
                <td><?= htmlspecialchars($c['notes']) ?></td>
                <td><?= $c['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
