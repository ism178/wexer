<?php
session_start();
require 'db.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = $_SESSION['user_id'];
        $type = $_POST['invoice_type'] ?? null; // "invoice" or "estimate"
        $date = $_POST['invoice_date'] ?? null;
        $bill_to = $_POST['bill_to'] ?? null;
        $address = $_POST['address'] ?? null;
        $notes = $_POST['notes'] ?? null;
        $subtotal = $_POST['subtotal'] ?? null;
        $total = $_POST['total'] ?? null;
        $apply_square_fee = isset($_POST['apply_square_fee']) ? 1 : 0;

        // Validate required fields
        if (!$type || !$date || !$bill_to || !$address || !$subtotal || !$total) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
            exit();
        }

        // Debugging: Log the received data
        error_log("Saving invoice: user_id=$user_id, type=$type, date=$date, bill_to=$bill_to, address=$address, subtotal=$subtotal, total=$total, apply_square_fee=$apply_square_fee");

        // Save the invoice/estimate details
        $stmt = $pdo->prepare("INSERT INTO invoices (user_id, type, date, bill_to, address, notes, subtotal, total, apply_square_fee) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $type, $date, $bill_to, $address, $notes, $subtotal, $total, $apply_square_fee])) {
            // Retrieve the last inserted ID
            $invoice_id = $pdo->lastInsertId();

            if (!$invoice_id) {
                echo json_encode(['success' => false, 'error' => 'Failed to retrieve the invoice ID.']);
                exit();
            }

            // Debugging: Log the successful save
            error_log("Invoice saved successfully with ID: $invoice_id");

            echo json_encode(['success' => true, 'invoice_id' => $invoice_id]);
            exit();
        } else {
            // Debugging: Log the failure
            error_log("Failed to execute the invoice save query.");
            echo json_encode(['success' => false, 'error' => 'Failed to save the invoice/estimate.']);
            exit();
        }
    } catch (PDOException $e) {
        // Debugging: Log the database error
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        exit();
    } catch (Exception $e) {
        // Debugging: Log the general error
        error_log("General error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit();
}
?>
