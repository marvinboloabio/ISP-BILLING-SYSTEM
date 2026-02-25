<?php
require_once '../includes/db_connect.php'; // adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?? null;
     $ref_num = $_POST['reference_num'] ?? null;
    $billing_date = $_POST['billing_date'] ?? null;
    $due_date = $_POST['due_date'] ?? null;

    if (!$customer_id || !$billing_date || !$due_date) {
        echo "All fields are required.";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO billing (customer_id,reference_num, billing_date, due_date) VALUES (?,?, ?, ?)");
    $stmt->bind_param("isss", $customer_id,$ref_num, $billing_date, $due_date);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>