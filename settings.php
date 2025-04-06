<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user details
$stmt = $pdo->prepare("SELECT first_name, last_name, username, usertag FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $usertag = trim($_POST['usertag']);

    // Check if username or usertag already exists for another user
    $check = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR usertag = ?) AND id != ?");
    $check->execute([$username, $usertag, $_SESSION['user_id']]);

    if ($check->rowCount() > 0) {
        $error = "Username or @usertag already taken.";
    } else {
        // Update user details
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, usertag = ? WHERE id = ?");
        if ($stmt->execute([$first_name, $last_name, $username, $usertag, $_SESSION['user_id']])) {
            $success = "Profile updated successfully.";
        } else {
            $error = "Failed to update profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="post" class="card p-4 shadow-sm">
                <h2 class="text-center">Settings</h2>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="usertag" class="form-label">@Usertag</label>
                    <input type="text" name="usertag" id="usertag" class="form-control" value="<?= htmlspecialchars($user['usertag']) ?>" required>
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
