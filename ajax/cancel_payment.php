<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id']; // reference_num is a string, do NOT cast to int

    $stmt = $conn->prepare("UPDATE billing SET status = 'Unpaid' WHERE reference_num = ?");
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Bill cancelled successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel bill.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>
