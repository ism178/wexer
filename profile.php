<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user details
$stmt = $pdo->prepare("SELECT first_name, last_name, username, usertag, profile_picture FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $uploadDir = 'img/profile_pictures/';

    // Ensure the directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory with proper permissions
    }

    // Validate file upload
    if ($file['error'] === UPLOAD_ERR_OK && in_array($file['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
        $fileName = $_SESSION['user_id'] . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $fileName;

        // Move the uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Update the database with the new profile picture path
            $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->execute([$filePath, $_SESSION['user_id']]);

            // Refresh the page to show the updated picture
            header("Location: profile.php?msg=uploaded");
            exit();
        } else {
            $error = "Failed to upload the profile picture.";
        }
    } else {
        $error = "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <h2 class="text-center">Profile</h2>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'uploaded') echo "<div class='alert alert-success'>Profile picture updated successfully.</div>"; ?>
                <div class="text-center mb-3">
                    <img src="<?= htmlspecialchars($user['profile_picture'] ?? 'img/user_placeholder.png') ?>" alt="Profile Picture" class="rounded-circle" width="150" height="150">
                </div>
                <form method="post" enctype="multipart/form-data" class="mb-3">
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Upload Profile Picture</label>
                        <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Upload Picture</button>
                </form>
                <div class="mb-3">
                    <strong>First Name:</strong> <?= htmlspecialchars($user['first_name']) ?>
                </div>
                <div class="mb-3">
                    <strong>Last Name:</strong> <?= htmlspecialchars($user['last_name']) ?>
                </div>
                <div class="mb-3">
                    <strong>Username:</strong> <?= htmlspecialchars($user['username']) ?>
                </div>
                <div class="mb-3">
                    <strong>@Usertag:</strong> <?= htmlspecialchars($user['usertag']) ?>
                </div>
                <a href="dashboard.php" class="btn btn-primary w-100">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
