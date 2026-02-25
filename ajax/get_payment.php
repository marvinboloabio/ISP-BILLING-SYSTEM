<?php
require_once '../includes/db_connect.php';

// Return JSON header
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(null);
    exit;
}

$payment_id = intval($_GET['id']);
if ($payment_id <= 0) {
    echo json_encode(null);
    exit;
}

// Query payment along with billing & customer details
$sql = "
    SELECT 
        p.id,
        p.bill_id,
        p.customer_id,
        p.reference_no,
        p.amount AS pay_amount,
        p.payment_date,
        p.payment_method,
        b.reference_num AS bill_ref,
        c.name AS customer_name,
        b.billing_date,
        b.due_date,
        c.amount AS amount_due
    FROM payments p
    JOIN billing b ON p.bill_id = b.id
    JOIN customers c ON p.customer_id = c.id
    WHERE p.id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Ensure dates are in YYYY-MM-DD format (they usually are if stored as DATE)
    // Cast numeric fields if needed
    $payment = [
        'id' => (int)$row['id'],
        'bill_id' => (int)$row['bill_id'],
        'customer_id' => (int)$row['customer_id'],
        'reference_no' => $row['reference_no'],
        'bill_ref' => $row['bill_ref'],
        'customer_name' => $row['customer_name'],
        'billing_date' => $row['billing_date'],
        'due_date' => $row['due_date'],
        'amount' => number_format((float)$row['amount_due'], 2, '.', ''),    // amount due
        'pay_amount' => number_format((float)$row['pay_amount'], 2, '.', ''), // payment amount
        'payment_date' => $row['payment_date'],
        'payment_method' => $row['payment_method'],
    ];
    echo json_encode($payment);
} else {
    echo json_encode(null);
}

$stmt->close();
$conn->close();
