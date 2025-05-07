<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto mt-10">
    <div class="flex justify-center">
        <div class="w-full max-w-4xl">
            <div class="bg-white p-6 shadow-md rounded-lg">
                <h2 id="form_title" class="text-center text-2xl font-bold mb-6">Create Invoice</h2>
                <form method="post" action="process_invoice.php">
                    <!-- Static Image -->
                    <div class="text-center mb-6">
                        <img src="247.jpeg" alt="Invoice Logo" class="mx-auto max-h-36">
                    </div>

                    <!-- Invoice Type Dropdown -->
                    <div class="mb-4">
                        <label for="invoice_type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="invoice_type" id="invoice_type" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="invoice">Invoice</option>
                            <option value="estimate">Estimate</option>
                        </select>
                    </div>

                    <!-- Date -->
                    <div class="mb-4">
                        <label for="invoice_date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="invoice_date" id="invoice_date" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>

                    <!-- Bill To -->
                    <div class="mb-4">
                        <label for="bill_to" class="block text-sm font-medium text-gray-700">Bill To</label>
                        <textarea name="bill_to" id="bill_to" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Enter billing details" required></textarea>
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" id="address" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" rows="2" placeholder="Enter address" required></textarea>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto mb-4">
                        <table class="min-w-full border-collapse border border-gray-300" id="items_table">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2">Qty</th>
                                    <th class="border border-gray-300 px-4 py-2">Description</th>
                                    <th class="border border-gray-300 px-4 py-2">Unit Price</th>
                                    <th class="border border-gray-300 px-4 py-2">Item Total</th>
                                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2"><input type="number" name="qty[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 qty" placeholder="0" min="0" required></td>
                                    <td class="border border-gray-300 px-4 py-2"><input type="text" name="description[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Item description" required></td>
                                    <td class="border border-gray-300 px-4 py-2"><input type="number" name="unit_price[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 unit_price" placeholder="0.00" min="0" step="0.01" required></td>
                                    <td class="border border-gray-300 px-4 py-2"><input type="text" name="item_total[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 item_total" placeholder="0.00" readonly></td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <button type="button" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 remove-row">&times;</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" id="add_row">+ Add Item</button>
                    </div>

                    <!-- Square Fee Checkbox -->
                    <div class="mb-4">
                        <label for="apply_square_fee" class="inline-flex items-center">
                            <input type="checkbox" id="apply_square_fee" class="form-checkbox h-5 w-5 text-indigo-600">
                            <span class="ml-2 text-sm font-medium text-gray-700">Apply 5% Square Fee</span>
                        </label>
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Enter any additional notes"></textarea>
                    </div>

                    <!-- Subtotal and Total -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="subtotal" class="block text-sm font-medium text-gray-700">Subtotal</label>
                            <input type="text" name="subtotal" id="subtotal" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00" readonly>
                        </div>
                        <div>
                            <label for="total" class="block text-sm font-medium text-gray-700">Total</label>
                            <input type="text" name="total" id="total" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00" readonly>
                        </div>
                    </div>
                </form>

                <!-- Removed the invoice number display section -->
                <!-- <div id="invoice_number_display" class="hidden text-center text-lg font-bold text-green-600 mt-4">
                    Invoice Number: <span id="invoice_number"></span>
                </div> -->

                <button type="button" id="download_pdf" class="mt-4 bg-green-500 text-white w-full py-2 rounded hover:bg-green-600">Download PDF</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
    // Add new row to the items table
    document.getElementById('add_row').addEventListener('click', function () {
        const tableBody = document.querySelector('#items_table tbody');
        const newRow = `
            <tr>
                <td class="border border-gray-300 px-4 py-2"><input type="number" name="qty[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 qty" placeholder="0" min="0" required></td>
                <td class="border border-gray-300 px-4 py-2"><input type="text" name="description[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Item description" required></td>
                <td class="border border-gray-300 px-4 py-2"><input type="number" name="unit_price[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 unit_price" placeholder="0.00" min="0" step="0.01" required></td>
                <td class="border border-gray-300 px-4 py-2"><input type="text" name="item_total[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 item_total" placeholder="0.00" readonly></td>
                <td class="border border-gray-300 px-4 py-2 text-center">
                    <button type="button" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 remove-row">&times;</button>
                </td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', newRow);
    });

    // Remove row from the items table
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            calculateTotals();
        }
    });

    // Calculate item total and update subtotal/total
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('qty') || e.target.classList.contains('unit_price')) {
            const row = e.target.closest('tr');
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit_price').value) || 0;
            const itemTotal = qty * unitPrice;
            row.querySelector('.item_total').value = itemTotal.toFixed(2);
            calculateTotals();
        }
    });

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item_total').forEach(function (itemTotal) {
            subtotal += parseFloat(itemTotal.value) || 0;
        });
        document.getElementById('subtotal').value = subtotal.toFixed(2);

        const applySquareFee = document.getElementById('apply_square_fee').checked;
        const squareFee = applySquareFee ? (subtotal * 0.05).toFixed(2) : 0;
        const total = (subtotal + parseFloat(squareFee)).toFixed(2);

        document.getElementById('total').value = total;
    }

    // Recalculate totals when the square fee checkbox is toggled
    document.getElementById('apply_square_fee').addEventListener('change', calculateTotals);

    // Dynamically set the title and form heading based on the type
    document.addEventListener('DOMContentLoaded', function () {
        const formTitle = document.getElementById('form_title');
        const invoiceType = document.getElementById('invoice_type');
        const pageTitle = document.querySelector('title');

        // Update the title and form heading on page load
        const updateTitles = () => {
            const type = invoiceType.value.charAt(0).toUpperCase() + invoiceType.value.slice(1);
            formTitle.textContent = `Create ${type}`;
            pageTitle.textContent = `Create ${type}`;
        };

        updateTitles();

        // Update the title and form heading when the dropdown value changes
        invoiceType.addEventListener('change', updateTitles);
    });

    document.getElementById('download_pdf').addEventListener('click', function () {
        const formData = new FormData(document.querySelector('form'));

        // Save the invoice/estimate data
        fetch('save_invoice.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response from save_invoice.php:', data); // Debugging log
            if (data.success) {
                // Proceed to generate the PDF
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                const logo = new Image();
                logo.src = '247.jpeg'; // Check this path

                logo.onload = function () {
                    // Title
                    const invoiceType = document.getElementById('invoice_type').value;
                    const title = invoiceType.charAt(0).toUpperCase() + invoiceType.slice(1);

                    doc.setFontSize(26);
                    doc.setTextColor(60, 60, 60);
                    doc.setFont("helvetica", "bold");
                    doc.text(title, 200, 20, { align: "right" });

                    // Line separator
                    doc.setDrawColor(160);
                    doc.setLineWidth(0.5);
                    doc.line(10, 25, 200, 25);

                    // Add logo
                    doc.addImage(logo, 'JPEG', 10, 30, 30, 30);

                    // Invoice details
                    const invoiceDate = document.getElementById('invoice_date').value;
                    const billTo = document.getElementById('bill_to').value;
                    const address = document.getElementById('address').value;
                    const notes = document.getElementById('notes').value;

                    doc.setFontSize(10);
                    doc.setFont("helvetica", "normal");
                    doc.setTextColor(50);

                    // Left column - Issued To
                    let y = 70;
                    doc.setFont("helvetica", "bold");
                    doc.text("ISSUED TO:", 10, y);
                    doc.setFont("helvetica", "normal");
                    doc.text(billTo, 10, y + 5);
                    doc.setFont("helvetica", "bold");
                    doc.text("ADDRESS:", 10, y + 15);
                    doc.setFont("helvetica", "normal");
                    doc.text(address, 10, y + 20);

                    // Right column - Invoice Info
                    doc.setFont("helvetica", "bold");
                    doc.text("INVOICE NO:", 140, y);
                    doc.setFont("helvetica", "normal");
                    doc.text(data.invoice_id.toString(), 180, y); // Use the invoice ID from the database
                    doc.setFont("helvetica", "bold");
                    doc.text("DATE:", 140, y + 5);
                    doc.setFont("helvetica", "normal");
                    doc.text(invoiceDate, 180, y + 5);

                    // Table headers
                    y += 30;
                    doc.setFont("helvetica", "bold");
                    doc.setFontSize(11);
                    doc.setTextColor(0);
                    doc.text("DESCRIPTION", 10, y);
                    doc.text("UNIT PRICE", 100, y);
                    doc.text("QTY", 140, y);
                    doc.text("TOTAL", 170, y);

                    // Line under headers
                    y += 2;
                    doc.setDrawColor(150);
                    doc.line(10, y, 200, y);
                    y += 5;

                    // Table content
                    doc.setFont("helvetica", "normal");
                    document.querySelectorAll('#items_table tbody tr').forEach(row => {
                        const qty = row.querySelector('.qty').value || "0";
                        const description = row.querySelector('input[name="description[]"]').value || "";
                        const unitPrice = row.querySelector('.unit_price').value || "0.00";
                        const itemTotal = row.querySelector('.item_total').value || "0.00";

                        doc.text(description, 10, y);
                        doc.text(unitPrice, 100, y);
                        doc.text(qty, 140, y);
                        doc.text(itemTotal, 170, y);
                        y += 7;
                    });

                    // Totals
                    const subtotal = document.getElementById('subtotal').value || "0.00";
                    const applySquareFee = document.getElementById('apply_square_fee').checked;
                    const squareFee = applySquareFee ? (parseFloat(subtotal) * 0.05).toFixed(2) : "0.00";
                    const total = document.getElementById('total').value || (parseFloat(subtotal) + parseFloat(squareFee)).toFixed(2);

                    y += 10;
                    doc.setFont("helvetica", "bold");
                    doc.text(`SUBTOTAL`, 140, y);
                    doc.text(`$${subtotal}`, 170, y);
                    y += 7;
                    if (applySquareFee) {
                        doc.text(`FEE (5%)`, 140, y);
                        doc.text(`$${squareFee}`, 170, y);
                        y += 7;
                    }
                    doc.text(`TOTAL`, 140, y);
                    doc.text(`$${total}`, 170, y);

                    // Notes
                    y += 15;
                    doc.setFont("helvetica", "bold");
                    doc.text("NOTES:", 10, y);
                    doc.setFont("helvetica", "normal");
                    y += 5;
                    doc.text(notes, 10, y, { maxWidth: 180 });

                    doc.save(`${title}_${data.invoice_id || 'new'}.pdf`);
                };
            } else {
                alert(data.error || 'Failed to save the invoice/estimate.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the invoice/estimate. Please try again.');
        });
    });
</script>
</body>
</html>
