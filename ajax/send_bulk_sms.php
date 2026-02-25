<?php
require_once '../includes/db_connect.php';

function sendDueReminder($contactNum, $name, $amount, $due_date) {
    $apiKey = 'eyJhbGciOiJIUzM4NCJ9.eyJzdWIiOiJMQjF4a09XRGZoZ0JVa3JjYzhPc1JHalY3ZTUzIiwiZXhwIjoxNzUxNTY0MzE4fQ.lXTp64gn97qMe9Iaq8-ldd9HC3dSR662THc1dcg90um-qqKOOUrVVQGvqrg9Xjf1';
    $message = "Good morning $name! Your ₱$amount internet bill is due on $due_date. Please settle to avoid disconnection. Thank you!";

    $headers = [
        "Authorization: Basic " . base64_encode("apikey:$apiKey"),
        "Content-Type: application/json"
    ];

    $payload = json_encode([
        [
            'mobile' => $contactNum,
            'text' => $message
        ]
    ]);

    $ch = curl_init("https://api.smstext.app/push");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    file_put_contents("../sms_debug_log.txt", "Manual trigger: $contactNum => $response\n", FILE_APPEND);

    if ($error) return false;

    return stripos($response, 'success') !== false || stripos($response, 'id') !== false;
}

$today = new DateTime();
$threshold_days = 3;

$sql = "SELECT b.*, c.name AS customer_name, c.contactNum AS contact, c.amount
        FROM billing b
        JOIN customers c ON b.customer_id = c.id
        WHERE LOWER(b.status) = 'unpaid' AND reminder_sent = 0";
$result = $conn->query($sql);

$sent_count = 0;

while ($row = $result->fetch_assoc()) {
    $due_date = new DateTime($row['due_date']);
    $interval = $today->diff($due_date)->days;

    if ($due_date >= $today && $interval <= $threshold_days) {
        $sent = sendDueReminder($row['contact'], $row['customer_name'], $row['amount'], $row['due_date']);
        if ($sent) {
            $conn->query("UPDATE billing SET reminder_sent = 1 WHERE id = " . $row['id']);
            $sent_count++;
        }
    }
}

$conn->close();

// ✅ Send proper JSON response
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'Reminders sent successfully.'
]);
exit;
