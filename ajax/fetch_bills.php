<?php
require_once '../includes/db_connect.php';

// ✅ Fetch all billing info with customer details
$sql = "SELECT b.*, c.name AS customer_name, c.contactNum AS contact, c.plan, c.amount
        FROM billing b
        JOIN customers c ON b.customer_id = c.id
        ORDER BY b.id DESC";
$result = $conn->query($sql);

$bills = [];

while ($row = $result->fetch_assoc()) {
    $bills[] = [
        'id' => (int)$row['id'],
        'reference_num' => $row['reference_num'],
        'customer_name' => $row['customer_name'],
        'contact' => $row['contact'],
        'customer_id' => (int)$row['customer_id'],
        'plan' => $row['plan'],
        'billing_date' => $row['billing_date'],
        'due_date' => $row['due_date'],
        'amount' => $row['amount'],
        'status' => strtolower($row['status']),
        'reminder_sent' => (int)$row['reminder_sent']
    ];
}

// ✅ Return JSON response for DataTable
header('Content-Type: application/json');
echo json_encode(['data' => $bills]);

$conn->close();
?>
