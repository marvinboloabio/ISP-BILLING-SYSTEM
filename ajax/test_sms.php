<?php
require_once '../includes/db_connect.php';

function sendDueReminder($contactNum, $name, $amount, $due_date) {
    $apiKey = 'eyJhbGciOiJIUzM4NCJ9.eyJzdWIiOiJMQjF4a09XRGZoZ0JVa3JjYzhPc1JHalY3ZTUzIiwiZXhwIjoxNzUxNTY0MzE4fQ.lXTp64gn97qMe9Iaq8-ldd9HC3dSR662THc1dcg90um-qqKOOUrVVQGvqrg9Xjf1';
    $message = "Hello $name! Your ₱$amount bill is due on $due_date. Please settle to avoid disconnection. Thank you!";

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

    // 🔍 Log debug info
    $log = "====== SMS Attempt ======\n";
    $log .= "Date: " . date('Y-m-d H:i:s') . "\n";
    $log .= "To: $contactNum\n";
    $log .= "Message: $message\n";
    $log .= "Payload: $payload\n";
    $log .= $error ? "❌ cURL ERROR: $error\n" : "✅ Response: $response\n";
    $log .= "==========================\n\n";

    echo "<pre>$log</pre>";
    file_put_contents("sms_debug_log.txt", $log, FILE_APPEND);

    if ($error) return false;

    return stripos($response, 'success') !== false || stripos($response, 'queued') !== false || stripos($response, 'id') !== false;
}

sendDueReminder("09467175812", "Test User", "500.00", "2025-06-30");
