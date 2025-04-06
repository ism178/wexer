<?php
session_start();
require 'db.php';
require 'vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if table ID is provided
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$table_id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Check if the file is uploaded and is an Excel file
    if ($file['error'] === UPLOAD_ERR_OK && in_array($file['type'], ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])) {
        $filePath = $file['tmp_name'];

        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Debugging: Log the rows being processed
            error_log("Rows from Excel file: " . print_r($rows, true));

            // Skip the header row and process the data
            $stmt = $pdo->prepare("INSERT INTO customers (user_id, table_id, name, phone, address, notes) 
                                   VALUES (?, ?, ?, ?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE 
                                   name = VALUES(name), 
                                   phone = VALUES(phone), 
                                   address = VALUES(address), 
                                   notes = VALUES(notes)");
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header row

                // Validate row data
                $name = isset($row[0]) ? trim($row[0]) : null;
                $phone = isset($row[1]) ? trim($row[1]) : null;
                $address = isset($row[2]) ? trim($row[2]) : null;
                $notes = isset($row[3]) ? trim($row[3]) : null;

                if (empty($name) || empty($phone)) {
                    error_log("Skipping row $index due to missing required fields: " . print_r($row, true));
                    continue; // Skip rows with missing required fields
                }

                // Debugging: Log the data being inserted
                error_log("Inserting row $index: Name=$name, Phone=$phone, Address=$address, Notes=$notes, Table ID=$table_id");

                // Insert into the database
                $stmt->execute([$_SESSION['user_id'], $table_id, $name, $phone, $address, $notes]);
            }

            header("Location: dashboard.php?msg=imported");
            exit();
        } catch (Exception $e) {
            // Debugging: Log the exception
            error_log("Exception during file processing: " . $e->getMessage());
            $error = "Failed to process the file: " . $e->getMessage();
        }
    } else {
        $error = "Invalid file type. Please upload a valid Excel file.";
        error_log("Invalid file type: " . print_r($file, true));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Customers</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm">
                <h2 class="text-center">Import Customers</h2>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <div class="mb-3">
                    <label for="file" class="form-label">Upload Excel File</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".xlsx, .xls" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Import</button>
                <a href="dashboard.php" class="btn btn-link w-100 mt-2">← Back to Dashboard</a>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
