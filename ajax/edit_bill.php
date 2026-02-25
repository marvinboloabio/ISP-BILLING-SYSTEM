<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['bill_id']);
    $customer_id = intval($_POST['customer_id']);
    $billing_date = $_POST['billing_date'];
    $due_date = $_POST['due_date'];

    // Validate inputs
    if (!$id || !$customer_id || !$billing_date || !$due_date) {
        echo 'All fields are required.';
        exit;
    }

    $stmt = $conn->prepare("UPDATE billing SET customer_id = ?, billing_date = ?, due_date = ? WHERE id = ?");
    $stmt->bind_param("issi", $customer_id, $billing_date, $due_date, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Failed to update bill.';
    }

    $stmt->close();
} else {
    echo 'Invalid request.';
}

$conn->close();