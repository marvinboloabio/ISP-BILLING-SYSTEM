<?php
// get_bill.php

header('Content-Type: application/json');
require_once '../includes/db_connect.php';

// Check if 'id' parameter exists and is numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(null);
    exit;
}

$id = (int) $_GET['id'];

// Prepare and execute query
$sql = "SELECT * FROM billing WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $bill = $result->fetch_assoc();
    echo json_encode($bill);
} else {
    echo json_encode(null);
}

$stmt->close();
$conn->close();
?>
