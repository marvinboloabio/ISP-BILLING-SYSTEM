<?php
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim inputs
    $location = trim($_POST['location']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contactNum = trim($_POST['contact_no']);
    $address = trim($_POST['address']);
    $plan = trim($_POST['plan']);
    $amount = trim($_POST['amount']);
    $date_of_installation = !empty($_POST['installation_date']) ? trim($_POST['installation_date']) : null;
    $active = isset($_POST['subscription_active']) ? 1 : 0;

    // --- Validation ---

   // Check if account number already exists
if (!empty($email)) {
    $stmt = $conn->prepare("SELECT id FROM customers WHERE acountNum = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo 'Account number already exists.';
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
}

// Check if contact number already exists
$stmt = $conn->prepare("SELECT id FROM customers WHERE contactNum = ?");
$stmt->bind_param("s", $contactNum);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo 'Contact number already exists.';
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Optional: Check if same customer already exists based on name + address + plan
$stmt = $conn->prepare("SELECT id FROM customers WHERE name = ? AND address = ? AND plan = ?");
$stmt->bind_param("sss", $name, $address, $plan);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo 'Customer with the same name, address, and plan already exists.';
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();


    // --- Insert Customer ---
    $stmt = $conn->prepare("INSERT INTO customers (name, acountNum, contactNum, location, address, plan, amount, date_of_installation, subscription_active) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssisi", $name, $email, $contactNum, $location, $address, $plan, $amount, $date_of_installation, $active);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Failed to add customer. Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
