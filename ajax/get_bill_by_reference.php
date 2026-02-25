<?php
require '../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_GET['bill_reference_num']) || empty($_GET['bill_reference_num'])) {
    echo json_encode(['error' => 'Missing reference']);
    exit;
}

$ref = $conn->real_escape_string($_GET['bill_reference_num']);
$sql = "SELECT 
            billing.customer_id as customer_id , billing.id as bill_id,
            customers.name AS customer_name, customers.amount AS amount_due, 
            billing.billing_date,
            billing.due_date 
        FROM billing 
        INNER JOIN customers ON billing.customer_id = customers.id 
        WHERE billing.reference_num = '$ref' 
        LIMIT 1";
$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $data = $res->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Not found']);
}
?>