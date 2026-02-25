<?php
require_once '../includes/db_connect.php';

// We return plain text "success" or an error message
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Invalid request method.';
    exit;
}

// 1. Retrieve and validate POST fields
$payment_id   = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
$bill_id      = isset($_POST['bill_id']) ? intval($_POST['bill_id']) : 0;
$customer_id  = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;
$pay_amount   = isset($_POST['pay_amount']) ? trim($_POST['pay_amount']) : '';
$payment_date = isset($_POST['payment_date']) ? trim($_POST['payment_date']) : '';
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

// Basic presence checks
if ($payment_id <= 0) {
    echo 'Missing or invalid payment ID.';
    exit;
}
if ($bill_id <= 0 || $customer_id <= 0) {
    echo 'Missing or invalid bill or customer ID.';
    exit;
}
if ($pay_amount === '' || !is_numeric($pay_amount) || (float)$pay_amount < 0) {
    echo 'Invalid payment amount.';
    exit;
}
if (empty($payment_date)) {
    echo 'Payment date is required.';
    exit;
}
// Validate payment_date format YYYY-MM-DD
$dateObj = DateTime::createFromFormat('Y-m-d', $payment_date);
if (!$dateObj || $dateObj->format('Y-m-d') !== $payment_date) {
    echo 'Invalid payment date format.';
    exit;
}
$allowed_methods = ['Cash','Bank Transfer','GCash','PayMaya'];
if (!in_array($payment_method, $allowed_methods, true)) {
    echo 'Invalid payment method.';
    exit;
}

// 3. Update the payments record
$stmt = $conn->prepare("
    UPDATE payments
    SET amount = ?, payment_date = ?, payment_method = ?
    WHERE id = ?
");
if (!$stmt) {
    echo 'Server error (prepare update).';
    exit;
}
$amt_float = (float)$pay_amount;
$stmt->bind_param("dssi", $amt_float, $payment_date, $payment_method, $payment_id);

if ($stmt->execute()) {
    // Optionally: if you want to ensure billing.status remains 'Paid', you can update billing here.
    // For example, if you allow editing a payment, you might re-affirm billing status:
    /*
    $stmt2 = $conn->prepare("UPDATE billing SET status = 'Paid' WHERE id = ?");
    if ($stmt2) {
        $stmt2->bind_param("i", $bill_id);
        $stmt2->execute();
        $stmt2->close();
    }
    */
    echo 'success';
} else {
    echo 'Failed to update payment.';
}

$stmt->close();
$conn->close();
