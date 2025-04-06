<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch customer details
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $customer = $stmt->fetch();

    if (!$customer) {
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['table_id'], $_POST['name'], $_POST['phone'])) {
    $id = (int)$_POST['id'];
    $table_id = (int)$_POST['table_id'];
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;

    $stmt = $pdo->prepare("UPDATE customers SET name = ?, phone = ?, address = ?, notes = ? WHERE id = ? AND table_id = ? AND user_id = ?");
    $stmt->execute([$name, $phone, $address, $notes, $id, $table_id, $_SESSION['user_id']]);

    header("Location: dashboard.php?id=$table_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="post" class="card p-4 shadow-sm">
                <h2 class="text-center">Edit Customer</h2>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <div class="mb-3">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($customer['id']) ?>">
                    <input type="hidden" name="table_id" value="<?= htmlspecialchars($customer['table_id']) ?>">
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($customer['name']) ?>" placeholder="Customer Name" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($customer['phone']) ?>" placeholder="Phone Number" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($customer['address']) ?>" placeholder="Address">
                </div>
                <div class="mb-3">
                    <textarea name="notes" class="form-control" placeholder="Notes"><?= htmlspecialchars($customer['notes']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                <a href="dashboard.php" class="btn btn-link w-100 mt-2">← Back to Dashboard</a>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
