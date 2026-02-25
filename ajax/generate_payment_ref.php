<?php
require_once '../includes/db_connect.php'; // adjust path if needed

$res = $conn->query("SELECT reference_no FROM payments ORDER BY id DESC LIMIT 1");
$lastRef = $res->fetch_assoc()['reference_no'] ?? null;

if ($lastRef && preg_match('/PAY-(\d+)/', $lastRef, $matches)) {
    $num = (int) $matches[1];
    $num++;
} else {
    $num = 1;
}

$newRef = 'PAY-' . str_pad($num, 4, '0', STR_PAD_LEFT);
echo json_encode(['reference' => $newRef]);