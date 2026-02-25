<?php
// Enable error reporting (remove this on production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure no whitespace before this line
header('Content-Type: application/json');

require_once '../includes/db_connect.php';

$data = [];

$sql = "
  SELECT p.*, b.reference_num AS bill_ref, c.name AS customer_name, b.billing_date
  FROM payments p
  LEFT JOIN customers c ON p.customer_id = c.id
  LEFT JOIN billing b ON p.bill_id = b.id
  WHERE b.status = 'Paid'
  ORDER BY p.id DESC
";

$result = $conn->query($sql);

// Check if query failed
if (!$result) {
  $error = ['error' => 'SQL Error: ' . $conn->error];
  echo json_encode($error);
  exit;
}

// Build the data array
while ($row = $result->fetch_assoc()) {
  $data[] = [
    'id' => $row['id'],
     'bill_ref' => $row['bill_ref'],
    'customer_name' => $row['customer_name'],
    'billing_date' => $row['billing_date'],
    'payment_date' => $row['payment_date'],
    'amount' => $row['amount'],
    'payment_method' => $row['payment_method'],
    'reference_no' => $row['reference_no']
  ];
}

// Log output to a debug file (optional)
file_put_contents('debug_output.txt', json_encode(['data' => $data], JSON_PRETTY_PRINT));

// Return JSON
echo json_encode(['data' => $data]);
?>
