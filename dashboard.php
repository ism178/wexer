<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch user details
$stmt = $pdo->prepare("SELECT first_name, last_name, usertag, profile_picture FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch all tables and their customers
$stmt = $pdo->prepare("SELECT * FROM tables WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$tables = $stmt->fetchAll();

$tables_with_customers = [];
foreach ($tables as $table) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE user_id = ? AND table_id = ?");
    $stmt->execute([$_SESSION['user_id'], $table['id']]);
    $customers = $stmt->fetchAll();
    $tables_with_customers[] = [
        'table' => $table,
        'customers' => $customers
    ];
}

// Capture PHP errors and pass them to JavaScript
ob_start();
?>
<script>

</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Customer Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="manage_tables.php">
                        <i class="bi bi-table"></i> Manage Tables
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="invoices.php">
                        <i class="bi bi-receipt"></i> Invoices
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= htmlspecialchars($user['profile_picture'] ?? 'img/user_placeholder.png') ?>" alt="User" class="rounded-circle me-2" width="40" height="40">
                        <span><?= htmlspecialchars($user['usertag']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center">All Tables and Customers</h2>
        <a href="manage_tables.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i>
        </a>
    </div>
    <?php foreach ($tables_with_customers as $table_data): ?>
        <?php
        // Determine sorting column and order for the current table
        $current_table_id = $table_data['table']['id'];
        $table_sort_column = isset($_GET["sort_$current_table_id"]) && in_array($_GET["sort_$current_table_id"], $valid_columns) ? $_GET["sort_$current_table_id"] : 'created_at';
        $table_sort_order = isset($_GET["order_$current_table_id"]) && $_GET["order_$current_table_id"] === 'asc' ? 'asc' : 'desc';

        // Sort customers for the current table
        usort($table_data['customers'], function ($a, $b) use ($table_sort_column, $table_sort_order) {
            if ($table_sort_order === 'asc') {
                return strcmp($a[$table_sort_column], $b[$table_sort_column]);
            }
            return strcmp($b[$table_sort_column], $a[$table_sort_column]);
        });
        ?>
        <div class="card mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span><?= htmlspecialchars($table_data['table']['title']) ?></span>
                <div>
                    <a href="import_customers.php?id=<?= $current_table_id ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                    </a>
                    <a href="edit_table.php?id=<?= $current_table_id ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form method="post" action="manage_tables.php" class="d-inline">
                        <input type="hidden" name="delete_table_id" value="<?= $current_table_id ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this table? This action cannot be undone.');">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Notes</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($table_data['customers'])): ?>
                            <?php foreach ($table_data['customers'] as $customer): ?>
                                <tr>
                                    <td><?= htmlspecialchars($customer['name']) ?></td>
                                    <td><?= htmlspecialchars($customer['phone']) ?></td>
                                    <td><?= htmlspecialchars($customer['address']) ?></td>
                                    <td><?= htmlspecialchars($customer['notes']) ?></td>
                                    <td><?= $customer['created_at'] ?></td>
                                    <td>
                                        <a href="edit_customer.php?id=<?= $customer['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="delete_customer.php?id=<?= $customer['id'] ?>&table_id=<?= $current_table_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this customer?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <!-- Add Customer Row -->
                        <tr>
                            <form method="post" action="add_customer.php">
                                <input type="hidden" name="table_id" value="<?= $current_table_id ?>">
                                <td>
                                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                                </td>
                                <td>
                                    <input type="text" name="phone" class="form-control" placeholder="Phone" required>
                                </td>
                                <td>
                                    <input type="text" name="address" class="form-control" placeholder="Address">
                                </td>
                                <td>
                                    <input type="text" name="notes" class="form-control" placeholder="Notes">
                                </td>
                                <td>-</td>
                                <td>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle"></i> Add
                                    </button>
                                </td>
                            </form>
                        </tr>
                    </tbody>
                </table>
                <?php if (empty($table_data['customers'])): ?>
                    <p class="text-muted">No customers in this table. Add the first customer using the form above.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
